@extends('dashboard.dashLayout')

@section('title', $user['name'] ?? 'User Profile')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left: Basic Info -->
        <div class="col-span-1">
            <div class="mb-4">
                <img src="{{ $user['photo_url'] ?? '/assets/images/user-placeholder.jpg' }}" alt="User Photo" class="w-48 h-48 object-cover rounded-full mx-auto">
            </div>
            <h2 class="text-3xl font-bold text-center mb-2">{{ $user['name'] ?? 'User Name' }}</h2>
            <p class="text-center text-gray-600 mb-2">{{ $user['email'] ?? 'Email not available' }}</p>
            <div class="text-center text-gray-500 mb-2">
                <span class="font-semibold">Phone:</span> {{ $user['phone'] ?? 'N/A' }}
            </div>
            <div class="text-center text-gray-500 mb-2">
                <span class="font-semibold">Address:</span> {{ $user['address'] ?? 'N/A' }}
            </div>
            <div class="text-center text-gray-500 mb-2">
                <span class="font-semibold">Role:</span> {{ ucfirst($user['role'] ?? 'N/A') }}
            </div>
        </div>

        <!-- Right: Additional Details -->
        <div class="col-span-2">
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Account Details</h3>
                <ul class="list-disc pl-6 text-gray-700">
                    <li><span class="font-semibold">Created At:</span> {{ $user['created_at'] }}</li>
                    <li><span class="font-semibold">Updated At:</span> {{ $user['updated_at'] }}</li>
                </ul>
            </div>

            <!-- Edit Button -->
            <div class="text-right">
                {{-- <a href="{{ route('user.edit', ['id' => $user['id']]) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Edit Profile</a> --}}
            </div>
        </div>
    </div>
</div>
@endsection
