<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
            'role' => 'in:customer,karenderia_owner'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'customer',
            'verified' => false
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'displayName' => $user->name,
                'role' => $user->role,
                'verified' => $user->verified
            ],
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    /**
     * Register a new karenderia owner with business details
     */
    public function registerKarenderiaOwner(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            // User account validation
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
            
            // Business information validation
            'business_name' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'address' => 'required|string|min:10',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            
            // Location coordinates (optional - admin will set these)
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            
            // Optional business fields
            'phone' => 'nullable|string|max:20',
            'business_email' => 'nullable|email|max:255',
            'opening_time' => 'nullable|string',
            'closing_time' => 'nullable|string',
            'operating_days' => 'nullable|array',
            'delivery_fee' => 'nullable|numeric|min:0',
            'delivery_time_minutes' => 'nullable|integer|min:0',
            'accepts_cash' => 'boolean',
            'accepts_online_payment' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'karenderia_owner',
                'verified' => false
            ]);

            // Create karenderia business record
            $karenderia = $user->karenderia()->create([
                'business_name' => $request->business_name,
                'description' => $request->description,
                'address' => $request->address,
                'city' => $request->city,
                'province' => $request->province,
                'latitude' => $request->latitude, // Will be null initially
                'longitude' => $request->longitude, // Will be null initially
                'phone' => $request->phone,
                'business_email' => $request->business_email,
                'opening_time' => $request->opening_time ?? '09:00',
                'closing_time' => $request->closing_time ?? '21:00',
                'operating_days' => json_encode($request->operating_days ?? []),
                'delivery_fee' => $request->delivery_fee ?? 0,
                'delivery_time_minutes' => $request->delivery_time_minutes ?? 30,
                'accepts_cash' => $request->accepts_cash ?? true,
                'accepts_online_payment' => $request->accepts_online_payment ?? false,
                'status' => 'pending', // Requires admin approval
                'approved_at' => null,
                'approved_by' => null
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Karenderia owner registration successful. Your application is now pending admin approval.',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'displayName' => $user->name,
                    'role' => $user->role,
                    'verified' => $user->verified
                ],
                'karenderia' => [
                    'id' => $karenderia->id,
                    'business_name' => $karenderia->business_name,
                    'status' => $karenderia->status,
                    'address' => $karenderia->address
                ],
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'displayName' => $user->name,
                'role' => $user->role,
                'verified' => $user->verified
            ],
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'displayName' => $user->name,
                'role' => $user->role,
                'verified' => $user->verified
            ]
        ]);
    }

    /**
     * Reset password (placeholder)
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // TODO: Implement actual password reset logic
        return response()->json([
            'message' => 'Password reset email sent'
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8',
            'new_password_confirmation' => 'required|same:new_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 401);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Verify email (placeholder)
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        // TODO: Implement email verification logic
        return response()->json([
            'message' => 'Email verified successfully'
        ]);
    }

    /**
     * Resend verification email (placeholder)
     */
    public function resendVerification(Request $request): JsonResponse
    {
        // TODO: Implement resend verification logic
        return response()->json([
            'message' => 'Verification email sent'
        ]);
    }
}
