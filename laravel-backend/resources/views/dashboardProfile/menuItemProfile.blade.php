@extends('dashboard.dashLayout')

@section('title', $menuItem['name'] ?? 'Menu Item Details')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex flex-col md:flex-row md:space-x-8">
        <!-- Left: Basic Info -->
        <div class="md:w-1/3 mb-6 md:mb-0">
            <div class="mb-4">
                <img src="{{ $menuItem['image_url'] ?? '/assets/images/food-placeholder.jpg' }}" alt="Menu Item" class="w-32 h-32 object-cover rounded-full mx-auto">
            </div>
            <h2 class="text-2xl font-bold text-center mb-2">{{ $menuItem['name'] ?? 'Menu Item Name' }}</h2>
            <p class="text-center text-gray-600 mb-2">{{ $menuItem['description'] ?? 'Description goes here.' }}</p>
            <div class="text-center text-gray-500 mb-2">
                <span class="font-semibold">Price:</span> ₱{{ $menuItem['price'] ?? '0.00' }}
            </div>
            <div class="text-center text-gray-500 mb-2">
                <span class="font-semibold">Category:</span> {{ $menuItem['category'] ?? 'N/A' }}
            </div>
            <div class="text-center text-gray-500 mb-2">
                <span class="font-semibold">Availability:</span> {{ ($menuItem['is_available'] ?? false) ? 'Available' : 'Not Available' }}
            </div>
        </div>
        <!-- Right: Additional Details -->
        <div class="md:w-2/3">
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Ingredients</h3>
                <ul class="list-disc pl-6 text-gray-700">
                    @forelse($menuItem['ingredients'] ?? [] as $ingredient)
                        <li>{{ $ingredient }}</li>
                    @empty
                        <li>No ingredients listed.</li>
                    @endforelse
                </ul>
            </div>
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Allergens</h3>
                <ul class="list-disc pl-6 text-gray-700">
                    @forelse($menuItem['allergens'] ?? [] as $allergen)
                        <li>{{ $allergen }}</li>
                    @empty
                        <li>No allergens listed.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
