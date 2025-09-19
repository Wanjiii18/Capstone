@extends('dashboard.dashLayout')

@section('title', 'Carinderia Dashboard')

@section('stats')
{{-- Optionally add stats specific to Carinderias here --}}
@endsection

@section('content')
<div class="bg-white p-4 rounded-lg shadow">
    <h2 class="text-lg font-bold mb-4">Carinderia Dashboard</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($karenderias as $karenderia)
        <div class="border rounded p-3">
            <h3 class="font-semibold">{{ $karenderia['name'] }}</h3>
            <p class="text-sm text-gray-600">{{ $karenderia['description'] }}</p>
            <a href="{{ route('dashboardProfile.karenderiaProfile', ['id' => $karenderia['id']]) }}" class="text-blue-600 underline text-sm">View Details</a>
        </div>
        @empty
        <p>No karenderias available.</p>
        @endforelse
    </div>
</div>
@endsection
