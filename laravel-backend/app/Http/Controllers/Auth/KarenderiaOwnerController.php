<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Karenderia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class KarenderiaOwnerController extends Controller
{
    /**
     * Register a new karenderia owner
     */
    public function register(Request $request): JsonResponse
    {
        try {
            // Validate owner registration data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone_number' => 'required|string|max:15',
                'password' => 'required|string|min:6|confirmed',
                
                // Karenderia details
                'karenderia_name' => 'required|string|max:255',
                'business_name' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'address' => 'required|string|max:500',
                'city' => 'required|string|max:100',
                'province' => 'required|string|max:100',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'business_phone' => 'required|string|max:15',
                'business_email' => 'nullable|email',
                'opening_time' => 'required|date_format:H:i',
                'closing_time' => 'required|date_format:H:i',
                'operating_days' => 'required|array|min:1',
                'operating_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'business_permit' => 'nullable|string',
                'delivery_fee' => 'required|numeric|min:0',
                'delivery_time_minutes' => 'required|integer|min:5|max:120',
                'accepts_cash' => 'boolean',
                'accepts_online_payment' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create the owner user account
            $owner = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'role' => 'karenderia_owner',
                'verified' => false, // Require admin verification
                'application_status' => 'pending'
            ]);

            // Create the karenderia
            $karenderia = Karenderia::create([
                'name' => $request->karenderia_name,
                'business_name' => $request->business_name,
                'description' => $request->description,
                'address' => $request->address,
                'city' => $request->city,
                'province' => $request->province,
                'phone' => $request->business_phone,
                'email' => $request->business_email,
                'business_email' => $request->business_email,
                'owner_id' => $owner->id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'opening_time' => $request->opening_time,
                'closing_time' => $request->closing_time,
                'operating_days' => $request->operating_days,
                'status' => 'pending', // Require admin approval
                'business_permit' => $request->business_permit,
                'delivery_fee' => $request->delivery_fee,
                'delivery_time_minutes' => $request->delivery_time_minutes,
                'accepts_cash' => $request->accepts_cash ?? true,
                'accepts_online_payment' => $request->accepts_online_payment ?? false,
                'average_rating' => 4.0, // Default rating
                'total_reviews' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Karenderia registration submitted successfully! Please wait for admin approval.',
                'data' => [
                    'owner' => [
                        'id' => $owner->id,
                        'name' => $owner->name,
                        'email' => $owner->email,
                        'application_status' => $owner->application_status
                    ],
                    'karenderia' => [
                        'id' => $karenderia->id,
                        'name' => $karenderia->name,
                        'status' => $karenderia->status,
                        'address' => $karenderia->address
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login karenderia owner
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('email', $request->email)
                       ->where('role', 'karenderia_owner')
                       ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Check if account is verified
            if (!$user->verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account pending admin approval',
                    'status' => $user->application_status
                ], 403);
            }

            // Create API token
            $token = $user->createToken('karenderia-owner-token')->plainTextToken;

            // Get owner's karenderia
            $karenderia = $user->karenderia;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'token' => $token,
                    'owner' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role
                    ],
                    'karenderia' => $karenderia ? [
                        'id' => $karenderia->id,
                        'name' => $karenderia->name,
                        'status' => $karenderia->status,
                        'address' => $karenderia->address,
                        'latitude' => $karenderia->latitude,
                        'longitude' => $karenderia->longitude
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get owner's karenderia profile
     */
    public function getProfile(Request $request): JsonResponse
    {
        try {
            $owner = $request->user();
            $karenderia = $owner->karenderia;

            if (!$karenderia) {
                return response()->json([
                    'success' => false,
                    'message' => 'No karenderia found for this owner'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'owner' => [
                        'id' => $owner->id,
                        'name' => $owner->name,
                        'email' => $owner->email,
                        'phone_number' => $owner->phone_number
                    ],
                    'karenderia' => [
                        'id' => $karenderia->id,
                        'name' => $karenderia->name,
                        'business_name' => $karenderia->business_name,
                        'description' => $karenderia->description,
                        'address' => $karenderia->address,
                        'city' => $karenderia->city,
                        'province' => $karenderia->province,
                        'phone' => $karenderia->phone,
                        'business_email' => $karenderia->business_email,
                        'latitude' => $karenderia->latitude,
                        'longitude' => $karenderia->longitude,
                        'opening_time' => $karenderia->opening_time,
                        'closing_time' => $karenderia->closing_time,
                        'operating_days' => $karenderia->operating_days,
                        'status' => $karenderia->status,
                        'average_rating' => $karenderia->average_rating,
                        'delivery_fee' => $karenderia->delivery_fee,
                        'delivery_time_minutes' => $karenderia->delivery_time_minutes,
                        'accepts_cash' => $karenderia->accepts_cash,
                        'accepts_online_payment' => $karenderia->accepts_online_payment
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update karenderia profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $owner = $request->user();
            $karenderia = $owner->karenderia;

            if (!$karenderia) {
                return response()->json([
                    'success' => false,
                    'message' => 'No karenderia found for this owner'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'business_name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string|max:1000',
                'address' => 'sometimes|string|max:500',
                'city' => 'sometimes|string|max:100',
                'province' => 'sometimes|string|max:100',
                'latitude' => 'sometimes|numeric|between:-90,90',
                'longitude' => 'sometimes|numeric|between:-180,180',
                'phone' => 'sometimes|string|max:15',
                'business_email' => 'sometimes|email',
                'opening_time' => 'sometimes|date_format:H:i',
                'closing_time' => 'sometimes|date_format:H:i',
                'operating_days' => 'sometimes|array|min:1',
                'operating_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'delivery_fee' => 'sometimes|numeric|min:0',
                'delivery_time_minutes' => 'sometimes|integer|min:5|max:120',
                'accepts_cash' => 'sometimes|boolean',
                'accepts_online_payment' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update only provided fields
            $karenderia->update($request->only([
                'name', 'business_name', 'description', 'address', 'city', 'province',
                'latitude', 'longitude', 'phone', 'business_email', 'opening_time',
                'closing_time', 'operating_days', 'delivery_fee', 'delivery_time_minutes',
                'accepts_cash', 'accepts_online_payment'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'karenderia' => $karenderia->fresh()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}