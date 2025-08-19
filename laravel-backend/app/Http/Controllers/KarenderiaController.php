<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class KarenderiaController extends Controller
{
    /**
     * Get all karenderias
     */
    public function index(): JsonResponse
    {
        // For now, return mock data that matches your frontend expectations
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
                'imageUrl' => '/assets/images/restaurant-placeholder.jpg',
                'deliveryTime' => '25-35 min',
                'deliveryFee' => 25,
                'minimumOrder' => 150,
                'isVerified' => true,
                'specialties' => ['Adobo', 'Lechon', 'Kare-kare'],
                'operatingHours' => [
                    'monday' => '8:00 AM - 9:00 PM',
                    'tuesday' => '8:00 AM - 9:00 PM',
                    'wednesday' => '8:00 AM - 9:00 PM',
                    'thursday' => '8:00 AM - 9:00 PM',
                    'friday' => '8:00 AM - 10:00 PM',
                    'saturday' => '8:00 AM - 10:00 PM',
                    'sunday' => '9:00 AM - 9:00 PM'
                ]
            ],
            [
                'id' => 2,
                'name' => 'Tita Rosa\'s Lutong Bahay',
                'description' => 'Traditional Filipino dishes made with love',
                'address' => '456 Colon St, Cebu City',
                'latitude' => 10.2937,
                'longitude' => 123.9013,
                'rating' => 4.3,
                'isOpen' => true,
                'cuisine' => 'Filipino',
                'priceRange' => '₱',
                'imageUrl' => '/assets/images/restaurant-placeholder.jpg',
                'deliveryTime' => '20-30 min',
                'deliveryFee' => 20,
                'minimumOrder' => 100,
                'isVerified' => true,
                'specialties' => ['Sinigang', 'Tinola', 'Bistek'],
                'operatingHours' => [
                    'monday' => '7:00 AM - 8:00 PM',
                    'tuesday' => '7:00 AM - 8:00 PM',
                    'wednesday' => '7:00 AM - 8:00 PM',
                    'thursday' => '7:00 AM - 8:00 PM',
                    'friday' => '7:00 AM - 9:00 PM',
                    'saturday' => '7:00 AM - 9:00 PM',
                    'sunday' => '8:00 AM - 8:00 PM'
                ]
            ],
            [
                'id' => 3,
                'name' => 'Kuya Jun\'s Carinderia',
                'description' => 'Quick and affordable Filipino meals',
                'address' => '789 Lahug, Cebu City',
                'latitude' => 10.3369,
                'longitude' => 123.9139,
                'rating' => 4.0,
                'isOpen' => false,
                'cuisine' => 'Filipino',
                'priceRange' => '₱',
                'imageUrl' => '/assets/images/restaurant-placeholder.jpg',
                'deliveryTime' => '15-25 min',
                'deliveryFee' => 15,
                'minimumOrder' => 80,
                'isVerified' => false,
                'specialties' => ['Pancit', 'Lumpia', 'Menudo'],
                'operatingHours' => [
                    'monday' => '6:00 AM - 7:00 PM',
                    'tuesday' => '6:00 AM - 7:00 PM',
                    'wednesday' => '6:00 AM - 7:00 PM',
                    'thursday' => '6:00 AM - 7:00 PM',
                    'friday' => '6:00 AM - 8:00 PM',
                    'saturday' => '6:00 AM - 8:00 PM',
                    'sunday' => 'Closed'
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $karenderias,
            'message' => 'Karenderias retrieved successfully'
        ]);
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

    /**
     * Get nearby karenderias within radius
     */
    public function nearby(Request $request): JsonResponse
    {
        try {
            $latitude = $request->query('latitude');
            $longitude = $request->query('longitude');
            $radius = $request->query('radius', 1000); // Default 1km

            if (!$latitude || !$longitude) {
                return response()->json([
                    'success' => false,
                    'message' => 'Latitude and longitude are required'
                ], 400);
            }

            // Get all active karenderias
            $karenderias = \App\Models\Karenderia::where('status', 'active')
                ->with(['owner:id,name,email', 'menuItems'])
                ->get();

            // Calculate distances and filter by radius
            $nearbyKarenderias = $karenderias->filter(function ($karenderia) use ($latitude, $longitude, $radius) {
                $distance = $this->calculateDistance(
                    $latitude, 
                    $longitude, 
                    $karenderia->latitude, 
                    $karenderia->longitude
                );
                
                // Add distance to the model
                $karenderia->distance = $distance;
                
                return $distance <= $radius;
            })->sortBy('distance')->values()->map(function ($karenderia) {
                return [
                    'id' => $karenderia->id,
                    'name' => $karenderia->name,
                    'description' => $karenderia->description,
                    'address' => $karenderia->address,
                    'latitude' => $karenderia->latitude,
                    'longitude' => $karenderia->longitude,
                    'distance' => round($karenderia->distance, 2), // Distance in meters
                    'rating' => $karenderia->average_rating,
                    'isOpen' => $this->isKarenderiaOpen($karenderia),
                    'cuisine' => 'Filipino',
                    'priceRange' => '₱₱',
                    'imageUrl' => $karenderia->logo_url ?: '/assets/images/restaurant-placeholder.jpg',
                    'deliveryTime' => $karenderia->delivery_time_minutes . ' min',
                    'deliveryFee' => $karenderia->delivery_fee,
                    'status' => $karenderia->status,
                    'phone' => $karenderia->phone,
                    'email' => $karenderia->email,
                    'operating_hours' => $this->formatOperatingHours($karenderia->operating_days),
                    'accepts_cash' => $karenderia->accepts_cash,
                    'accepts_online_payment' => $karenderia->accepts_online_payment,
                    'menu_items_count' => $karenderia->menuItems->count(),
                    'owner' => $karenderia->owner ? $karenderia->owner->name : 'Unknown'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $nearbyKarenderias,
                'meta' => [
                    'total' => $nearbyKarenderias->count(),
                    'radius' => $radius,
                    'center' => ['lat' => $latitude, 'lng' => $longitude]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get nearby karenderias',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate distance between two points in meters using Haversine formula
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $lat1Rad = deg2rad($lat1);
        $lng1Rad = deg2rad($lng1);
        $lat2Rad = deg2rad($lat2);
        $lng2Rad = deg2rad($lng2);

        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLng = $lng2Rad - $lng1Rad;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLng / 2) * sin($deltaLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
