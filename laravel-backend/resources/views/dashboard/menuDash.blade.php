@extends('dashboard.dashLayout')

@section('title', 'Meals Dashboard')

@section('tabs')
<div class="bg-white p-4 rounded-lg shadow mb-6">
    <div class="flex space-x-4 border-b pb-2">
        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600">Overview</a>
        <a href="{{ route('dashboard.karenderia') }}" class="text-gray-600 hover:text-blue-600">Carinderias</a>
        <a href="{{ route('dashboard.menu') }}" class="text-blue-600 border-b-2 border-blue-600 pb-1">Meals</a>
        <a href="{{ route('dashboard.users') }}" class="text-gray-600 hover:text-blue-600">Users</a>
        <a href="{{ route('dashboard.reports') }}" class="text-gray-600 hover:text-blue-600">Reports</a>
    </div>
</div>
@endsection

@section('content')
<div class="bg-white p-4 rounded-lg shadow">
    <h2 class="text-lg font-bold mb-4">Meals Dashboard</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($menuItems as $menuItem)
        <div class="border rounded p-3">
            <h3 class="font-semibold">{{ $menuItem['name'] }}</h3>
            <p class="text-sm text-gray-600">{{ $menuItem['description'] }}</p>
            <a href="{{ route('dashboardProfile.menuItemProfile', ['id' => $menuItem['id']]) }}" class="text-blue-600 underline text-sm">View Details</a>
        </div>
        @empty
        <p>No menu items available.</p>
        @endforelse
    </div>
</div>
@endsection
