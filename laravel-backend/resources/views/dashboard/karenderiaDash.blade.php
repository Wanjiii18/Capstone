@extends('dashboard.dashLayout')

@section('title', 'Carinderia Dashboard')

@section('stats')
{{-- Optionally add stats specific to Carinderias here --}}
@endsection

@section('tabs')
<div class="bg-white p-4 rounded-lg shadow mb-6">
    <div class="flex space-x-4 border-b pb-2">
        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600">Overview</a>
        <a href="{{ route('dashboard.karenderia') }}" class="text-blue-600 border-b-2 border-blue-600 pb-1">Carinderias</a>
        <a href="{{ route('dashboard.meals') }}" class="text-gray-600 hover:text-blue-600">Meals</a>
        <a href="{{ route('dashboard.users') }}" class="text-gray-600 hover:text-blue-600">Users</a>
        <a href="{{ route('dashboard.reports') }}" class="text-gray-600 hover:text-blue-600">Reports</a>
    </div>
</div>
@endsection

@section('content')
<div class="bg-white p-4 rounded-lg shadow">
    <h2 class="text-lg font-bold mb-4">Carinderia Dashboard</h2>
    <p>Content for Carinderia dashboard goes here.</p>
</div>
@endsection
