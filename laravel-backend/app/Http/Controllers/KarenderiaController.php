<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class KarenderiaController extends Controller
{
    /**
     * Get all karenderias (only approved ones for customers)
     */
    public function index(): JsonResponse
    {
        try {
            // Only return approved/active karenderias for customers
            $karenderias = \App\Models\Karenderia::where('status', 'active')
                ->with(['owner:id,name,email'])
                ->get()
                ->map(function ($karenderia) {
                    return [
                        'id' => $karenderia->id,
                        'name' => $karenderia->name,
                        'description' => $karenderia->description,
                        'address' => $karenderia->address,
                        'latitude' => $karenderia->latitude,
                        'longitude' => $karenderia->longitude,
                        'rating' => $karenderia->average_rating,
                        'isOpen' => $this->isKarenderiaOpen($karenderia),
                        'cuisine' => 'Filipino', // Default for now
                        'priceRange' => '₱₱',
                        'imageUrl' => $karenderia->logo_url ?: '/assets/images/restaurant-placeholder.jpg',
                        'deliveryTime' => $karenderia->delivery_time_minutes . ' min',
                        'deliveryFee' => $karenderia->delivery_fee,
                        'minimumOrder' => 100, // Default
                        'isVerified' => $karenderia->status === 'active',
                        'specialties' => ['Filipino Cuisine'], // Can be enhanced later
                        'phone' => $karenderia->phone,
                        'email' => $karenderia->email,
                        'operatingHours' => $this->formatOperatingHours($karenderia->operating_days),
                        'accepts_cash' => $karenderia->accepts_cash,
                        'accepts_online_payment' => $karenderia->accepts_online_payment,
                        'owner' => $karenderia->owner ? $karenderia->owner->name : 'Unknown',
                        'status' => $karenderia->status
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $karenderias,
                'message' => 'Approved karenderias retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve karenderias',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if karenderia is currently open
     */
    private function isKarenderiaOpen($karenderia): bool
    {
        if (!$karenderia->opening_time || !$karenderia->closing_time) {
            return true; // Default to open if no hours specified
        }
        
        $now = now();
        $currentDay = strtolower($now->format('l')); // monday, tuesday, etc.
        $currentTime = $now->format('H:i');
        
        // Check if today is in operating days
        if ($karenderia->operating_days && !in_array($currentDay, $karenderia->operating_days)) {
            return false;
        }
        
        // Check if current time is within operating hours
        return $currentTime >= $karenderia->opening_time && $currentTime <= $karenderia->closing_time;
    }

    /**
     * Format operating hours for frontend
     */
    private function formatOperatingHours($operatingDays): array
    {
        $defaultHours = [
            'monday' => '8:00 AM - 9:00 PM',
            'tuesday' => '8:00 AM - 9:00 PM',
            'wednesday' => '8:00 AM - 9:00 PM',
            'thursday' => '8:00 AM - 9:00 PM',
            'friday' => '8:00 AM - 10:00 PM',
            'saturday' => '8:00 AM - 10:00 PM',
            'sunday' => '9:00 AM - 9:00 PM'
        ];
        
        if (!$operatingDays) {
            return $defaultHours;
        }
        
        // If operating days is just an array of day names, use default hours
        if (is_array($operatingDays) && !isset($operatingDays['monday'])) {
            $hours = [];
            foreach ($defaultHours as $day => $time) {
                $hours[$day] = in_array($day, $operatingDays) ? $time : 'Closed';
            }
            return $hours;
        }
        
        return $operatingDays ?: $defaultHours;
    }

    /**
     * Get a specific karenderia
     */
    public function show($id): JsonResponse
    {
        // This would normally fetch from database
        // For now, return mock data
        $karenderia = [
            'id' => $id,
            'name' => 'Mama\'s Kitchen',
            'description' => 'Authentic Filipino home cooking',
            'address' => '123 Main St, Cebu City',
            'latitude' => 10.3157,
            'longitude' => 123.8854,
            'rating' => 4.5,
            'isOpen' => true,
            'cuisine' => 'Filipino',
            'priceRange' => '₱₱',
            'imageUrl' => '/assets/images/restaurant-placeholder.jpg',
            'deliveryTime' => '25-35 min',
            'deliveryFee' => 25,
            'minimumOrder' => 150,
            'isVerified' => true,
            'specialties' => ['Adobo', 'Lechon', 'Kare-kare']
        ];

        return response()->json([
            'success' => true,
            'data' => $karenderia,
            'message' => 'Karenderia retrieved successfully'
        ]);
    }

    /**
     * Get current user's karenderia application/restaurant
     */
    public function myKarenderia(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                return response()->json([
                    'success' => false,
                    'message' => 'No karenderia application found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $karenderia->id,
                    'name' => $karenderia->name,
                    'description' => $karenderia->description,
                    'address' => $karenderia->address,
                    'phone' => $karenderia->phone,
                    'email' => $karenderia->email,
                    'latitude' => $karenderia->latitude,
                    'longitude' => $karenderia->longitude,
                    'opening_time' => $karenderia->opening_time,
                    'closing_time' => $karenderia->closing_time,
                    'operating_days' => $karenderia->operating_days,
                    'delivery_fee' => $karenderia->delivery_fee,
                    'delivery_time_minutes' => $karenderia->delivery_time_minutes,
                    'accepts_cash' => $karenderia->accepts_cash,
                    'accepts_online_payment' => $karenderia->accepts_online_payment,
                    'status' => $karenderia->status,
                    'created_at' => $karenderia->created_at,
                    'updated_at' => $karenderia->updated_at,
                    'status_message' => $this->getStatusMessage($karenderia->status)
                ],
                'message' => 'Your karenderia information retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve karenderia information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status message for karenderia owner
     */
    private function getStatusMessage($status): string
    {
        switch ($status) {
            case 'pending':
                return 'Your karenderia application is under review. Please wait for admin approval.';
            case 'active':
                return 'Your karenderia is approved and active! Customers can now see your restaurant.';
            case 'inactive':
                return 'Your karenderia application was rejected or deactivated. Please contact admin for details.';
            default:
                return 'Status unknown. Please contact support.';
        }
    }

    /**
     * Store a new karenderia registration
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'address' => 'required|string',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'opening_time' => 'nullable|date_format:H:i',
                'closing_time' => 'nullable|date_format:H:i',
                'operating_days' => 'nullable|array',
                'delivery_fee' => 'nullable|numeric|min:0',
                'delivery_time_minutes' => 'nullable|integer|min:0',
                'accepts_cash' => 'boolean',
                'accepts_online_payment' => 'boolean'
            ]);

            $user = $request->user();
            
            // Check if user already has a karenderia
            $existingKarenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
            if ($existingKarenderia) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a karenderia registered',
                    'data' => $existingKarenderia
                ], 409);
            }

            // Create karenderia with pending status
            $karenderia = \App\Models\Karenderia::create([
                'owner_id' => $user->id,
                'status' => 'pending', // Will need admin approval
                ...$validatedData
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Karenderia application submitted successfully. Waiting for admin approval.',
                'data' => $karenderia
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit karenderia application',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search karenderias
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $location = $request->get('location', '');
        $cuisine = $request->get('cuisine', '');

        // This would normally search in database
        // For now, return filtered mock data
        $karenderias = [
            [
                'id' => 1,
                'name' => 'Mama\'s Kitchen',
                'description' => 'Authentic Filipino home cooking',
                'address' => '123 Main St, Cebu City',
                'latitude' => 10.3157,
                'longitude' => 123.8854,
                'rating' => 4.5,
                'isOpen' => true,
                'cuisine' => 'Filipino',
                'priceRange' => '₱₱',
                'deliveryTime' => '25-35 min',
                'deliveryFee' => 25,
                'minimumOrder' => 150,
                'isVerified' => true
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $karenderias,
            'message' => 'Search results retrieved successfully',
            'query' => $query,
            'filters' => [
                'location' => $location,
                'cuisine' => $cuisine
            ]
        ]);
    }

    /**
     * Update karenderia information
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $karenderia = \App\Models\Karenderia::findOrFail($id);
            $user = $request->user();
            
            // Check if user owns this karenderia
            if ($karenderia->owner_id !== $user->id && $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this karenderia'
                ], 403);
            }

            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'address' => 'sometimes|string',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'opening_time' => 'nullable|date_format:H:i',
                'closing_time' => 'nullable|date_format:H:i',
                'operating_days' => 'nullable|array',
                'delivery_fee' => 'nullable|numeric|min:0',
                'delivery_time_minutes' => 'nullable|integer|min:0',
                'accepts_cash' => 'boolean',
                'accepts_online_payment' => 'boolean'
            ]);

            $karenderia->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Karenderia updated successfully',
                'data' => $karenderia
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update karenderia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete karenderia
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $karenderia = \App\Models\Karenderia::findOrFail($id);
            $user = $request->user();
            
            // Check if user owns this karenderia or is admin
            if ($karenderia->owner_id !== $user->id && $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this karenderia'
                ], 403);
            }

            $karenderia->delete();

            return response()->json([
                'success' => true,
                'message' => 'Karenderia deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete karenderia',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
