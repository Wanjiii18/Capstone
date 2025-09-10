@extends('dashboard.dashLayout')

@section('title', 'Users Dashboard')

@section('stats')
{{-- Optionally add stats specific to Users here --}}
@endsection

@section('tabs')
<div class="bg-white p-4 rounded-lg shadow mb-6">
    <div class="flex space-x-4 border-b pb-2">
        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600">Overview</a>
        <a href="{{ route('dashboard.karenderia') }}" class="text-gray-600 hover:text-blue-600">Carinderias</a>
        <a href="{{ route('dashboard.menu') }}" class="text-gray-600 hover:text-blue-600">Meals</a>
        <a href="{{ route('dashboard.users') }}" class="text-blue-600 border-b-2 border-blue-600 pb-1">Users</a>
        <a href="{{ route('dashboard.reports') }}" class="text-gray-600 hover:text-blue-600">Reports</a>
    </div>
</div>
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
