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
}
