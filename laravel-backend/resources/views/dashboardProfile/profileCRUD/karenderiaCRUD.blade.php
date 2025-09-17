@extends('dashboard.dashLayout')

@section('title', 'Edit Karenderia')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-lg font-bold mb-4">Edit Karenderia</h2>
    <form action="{{ route('karenderia.update', ['id' => $karenderia->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ $karenderia->name }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $karenderia->description }}</textarea>
        </div>
        <div class="mb-4">
            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
            <input type="text" name="address" id="address" value="{{ $karenderia->address }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div class="mb-4">
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ $karenderia->phone }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ $karenderia->email }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="pending" {{ $karenderia->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $karenderia->status == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="active" {{ $karenderia->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $karenderia->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="rejected" {{ $karenderia->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Save Changes</button>
        </div>
    </form>
</div>
@endsection
