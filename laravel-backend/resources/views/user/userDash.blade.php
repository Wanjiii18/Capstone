@extends('dashboard.dashLayout')

@section('title', 'Users Dashboard')

@section('stats')
{{-- Optionally add stats specific to Users here --}}
@endsection

@section('content')
<div class="bg-white p-4 rounded-lg shadow">
    <h2 class="text-lg font-bold mb-4">Users Dashboard</h2>

    {{-- Filter Dropdown --}}
    <form method="GET" action="{{ route('dashboard.users') }}" class="mb-4">
        <label for="sort" class="block text-sm font-medium text-gray-700">Sort By:</label>
        <select name="sort" id="sort" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
            <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email</option>
            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Time Created</option>
        </select>
        <button type="submit" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded">Apply</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($users as $user)
        <div class="border rounded p-3">
            <h3 class="font-semibold">{{ $user->name }}</h3>
            <p class="text-sm text-gray-600">{{ $user->email }}</p>
            <a href="{{ route('dashboardProfile.userProfile', ['id' => $user->id]) }}" class="text-blue-600 underline text-sm">View Details</a>
        </div>
        @empty
        <p>No users available.</p>
        @endforelse
    </div>

    {{-- Pagination Links --}}
    <div class="mt-4">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>
@endsection
