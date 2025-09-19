@extends('dashboard.dashLayout')

@section('title', $karenderia['name'] ?? 'Karenderia Profile')

@section('content')
<div class="flex justify-center px-4">
    <div class="w-full max-w-6xl bg-white p-8 rounded-lg shadow">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- 🟢 Left: Basic Info -->
            <div class="flex flex-col items-center lg:items-start">
                <div class="mb-4">
                    <img src="{{ $karenderia['logo_url'] ?? '/assets/images/restaurant-placeholder.jpg' }}" 
                         alt="Logo" 
                         class="w-48 h-48 object-cover rounded-full mx-auto ring-4 ring-blue-100">
                </div>
                <h2 class="text-3xl font-bold text-center lg:text-left mb-2">
                    {{ $karenderia['name'] ?? 'Karenderia Name' }}
                </h2>
                <p class="text-center lg:text-justify text-gray-600 mb-4 leading-relaxed">
                    {{ $karenderia['description'] ?? 'Description goes here.' }}
                </p>

                <div class="mb-4">
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                        {{ ucfirst($karenderia['status'] ?? 'pending') }}
                    </span>
                </div>

                <div class="space-y-2 text-gray-500 text-sm">
                    <div><span class="font-semibold">Owner:</span> {{ $karenderia['owner_name'] ?? 'N/A' }}</div>
                    <div><span class="font-semibold">Contact:</span> {{ $karenderia['phone'] ?? 'N/A' }}</div>
                    <div><span class="font-semibold">Email:</span> {{ $karenderia['email'] ?? 'N/A' }}</div>
                    <div>
                        <span class="font-semibold">Location:</span> {{ $karenderia['address'] ?? 'N/A' }}
                    </div>
                    <div>
                        <span class="font-semibold">Coordinates:</span> 
                        {{ $karenderia['latitude'] ?? '-' }}, {{ $karenderia['longitude'] ?? '-' }}
                    </div>
                </div>
            </div>

            <!-- 🔵 Right: Details -->
            <div class="lg:col-span-2">
                <!-- Menu -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4 border-b pb-2">Menu</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @forelse($karenderia['menu'] ?? [] as $item)
                            <div class="border rounded-lg p-4 flex items-center hover:shadow transition">
                                <img src="{{ $item['image_url'] ?? '/assets/images/food-placeholder.jpg' }}" 
                                     alt="Menu Item" 
                                     class="w-20 h-20 object-cover rounded mr-4">
                                <div>
                                    <div class="font-semibold text-lg">{{ $item['name'] ?? 'Menu Item' }}</div>
                                    <div class="text-gray-500 text-sm text-justify mb-1">
                                        {{ $item['description'] ?? '' }}
                                    </div>
                                    <div class="text-green-700 font-bold">₱{{ $item['price'] ?? '0.00' }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-gray-400 col-span-full text-center">No menu items available.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Edit Button -->
                <div class="text-center">
                    <a href="{{ route('karenderia.edit', ['id' => $karenderia['id']]) }}" 
                       class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-lg shadow hover:bg-blue-700 transition">
                       Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
