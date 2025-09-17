@extends('dashboard.dashLayout')

@section('title', $karenderia['name'] ?? 'Karenderia Profile')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex flex-col md:flex-row md:space-x-8">
        <!-- Left: Basic Info -->
        <div class="md:w-1/3 mb-6 md:mb-0">
            <div class="mb-4">
                <img src="{{ $karenderia['logo_url'] ?? '/assets/images/restaurant-placeholder.jpg' }}" alt="Logo" class="w-32 h-32 object-cover rounded-full mx-auto">
            </div>
            <h2 class="text-2xl font-bold text-center mb-2">{{ $karenderia['name'] ?? 'Karenderia Name' }}</h2>
            <p class="text-center text-gray-600 mb-2">{{ $karenderia['description'] ?? 'Description goes here.' }}</p>
            <div class="text-center text-sm text-gray-500 mb-2">
                <span class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded">
                    {{ ucfirst($karenderia['status'] ?? 'pending') }}
                </span>
            </div>
            <div class="text-center text-gray-500 mb-2">
                <span class="font-semibold">Owner:</span> {{ $karenderia['owner_name'] ?? 'N/A' }}
            </div>
            <div class="text-center text-gray-500 mb-2">
                <span class="font-semibold">Contact:</span> {{ $karenderia['phone'] ?? 'N/A' }}<br>
                <span class="font-semibold">Email:</span> {{ $karenderia['email'] ?? 'N/A' }}
            </div>
            <div class="text-center text-gray-500 mb-2">
                <span class="font-semibold">Location:</span>
                {{ $karenderia['address'] ?? 'N/A' }}
                <br>
                <span class="font-semibold">Coordinates:</span>
                {{ $karenderia['latitude'] ?? '-' }}, {{ $karenderia['longitude'] ?? '-' }}
            </div>
            <div class="text-center text-gray-500 mb-2">
                <span class="font-semibold">Operating Days:</span>
                {{ is_array($karenderia['operating_days'] ?? null) ? implode(', ', $karenderia['operating_days']) : ($karenderia['operating_days'] ?? 'N/A') }}
                <br>
                <span class="font-semibold">Hours:</span>
                {{ ($karenderia['opening_time'] ?? 'N/A') . ' - ' . ($karenderia['closing_time'] ?? 'N/A') }}
            </div>
            <div class="text-center text-gray-500 mb-2">
                <span class="font-semibold">Delivery Fee:</span> ₱{{ $karenderia['delivery_fee'] ?? '0' }}<br>
                <span class="font-semibold">Delivery Time:</span> {{ $karenderia['delivery_time_minutes'] ?? '-' }} min
            </div>
            <div class="text-center text-gray-500 mb-2">
                <span class="font-semibold">Accepts Cash:</span> {{ ($karenderia['accepts_cash'] ?? false) ? 'Yes' : 'No' }}<br>
                <span class="font-semibold">Online Payment:</span> {{ ($karenderia['accepts_online_payment'] ?? false) ? 'Yes' : 'No' }}
            </div>
        </div>
        <!-- Right: Details -->
        <div class="md:w-2/3">
            <!-- Menu -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Menu</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($karenderia['menu'] ?? [] as $item)
                        <div class="border rounded p-3 flex items-center">
                            <img src="{{ $item['image_url'] ?? '/assets/images/food-placeholder.jpg' }}" alt="Menu Item" class="w-16 h-16 object-cover rounded mr-4">
                            <div>
                                <div class="font-semibold">{{ $item['name'] ?? 'Menu Item' }}</div>
                                <div class="text-gray-500 text-sm">{{ $item['description'] ?? '' }}</div>
                                <div class="text-green-700 font-bold">₱{{ $item['price'] ?? '0.00' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-400">No menu items available.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection