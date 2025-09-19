@extends('dashboard.dashLayout')

@section('title', $menuItem['name'] ?? 'Menu Item Details')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left: Basic Info -->
        <div class="col-span-1">
            <div class="mb-4">
                <img src="{{ $menuItem['image_url'] ?? '/assets/images/food-placeholder.jpg' }}" alt="Menu Item" class="w-48 h-48 object-cover rounded-full mx-auto">
            </div>
            <h2 class="text-3xl font-bold text-center mb-2">{{ $menuItem['name'] ?? 'Menu Item Name' }}</h2>
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
        <div class="col-span-2">
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

            <!-- Edit Button -->
            <div class="text-right">
                {{-- <a href="{{ route('menuItem.edit', ['id' => $menuItem['id']]) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Edit Profile</a> --}}
            </div>
        </div>
    </div>
</div>
@endsection
