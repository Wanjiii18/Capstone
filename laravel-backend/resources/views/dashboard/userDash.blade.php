@extends('dashboard.dashLayout')

@section('title', 'Users Dashboard')

@section('stats')
{{-- Optionally add stats specific to Users here --}}
@endsection

@section('content')
<div class="bg-white p-4 rounded-lg shadow">
    <h2 class="text-lg font-bold mb-4">Users Dashboard</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($users as $user)
        <div class="border rounded p-3">
            <h3 class="font-semibold">{{ $user['name'] }}</h3>
            <p class="text-sm text-gray-600">{{ $user['email'] }}</p>
            <a href="{{ route('dashboardProfile.userProfile', ['id' => $user['id']]) }}" class="text-blue-600 underline text-sm">View Details</a>
        </div>
        @empty
        <p>No users available.</p>
        @endforelse
    </div>
</div>
@endsection
