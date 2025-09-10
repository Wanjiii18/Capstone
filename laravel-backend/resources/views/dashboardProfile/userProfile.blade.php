@extends('dashboard.dashLayout')

@section('title', $user['name'] ?? 'User Profile')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex flex-col md:flex-row md:space-x-8">
        <!-- Left: Basic Info -->
        <div class="md:w-1/3 mb-6 md:mb-0">
            <div class="mb-4">
                <img src="{{ $user['photo_url'] ?? '/assets/images/user-placeholder.jpg' }}" alt="User Photo" class="w-32 h-32 object-cover rounded-full mx-auto">
            </div>
            <h2 class="text-2xl font-bold text-center mb-2">{{ $user['name'] ?? 'User Name' }}</h2>
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
        <div class="md:w-2/3">
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Account Details</h3>
                <ul class="list-disc pl-6 text-gray-700">
                    <li><span class="font-semibold">Created At:</span> {{ $user['created_at'] }}</li>
                    <li><span class="font-semibold">Updated At:</span> {{ $user['updated_at'] }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
