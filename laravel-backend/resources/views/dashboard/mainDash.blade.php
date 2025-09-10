@extends('dashboard.dashLayout')

@section('tabs')
<div class="bg-white p-4 rounded-lg shadow mb-6">
    <div class="flex space-x-4 border-b pb-2">
        <a href="{{ route('dashboard') }}" class="text-blue-600 border-b-2 border-blue-600 pb-1">Overview</a>
        <a href="{{ route('dashboard.karenderia') }}" class="text-gray-600 hover:text-blue-600">Carinderias</a>
        <a href="{{ route('dashboard.menu') }}" class="text-gray-600 hover:text-blue-600">Meals</a>
        <a href="{{ route('dashboard.users') }}" class="text-gray-600 hover:text-blue-600">Users</a>
        <a href="{{ route('dashboard.reports') }}" class="text-gray-600 hover:text-blue-600">Reports</a>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mt-4">Main Dashboard</h1>
            <p>Welcome to the main dashboard. Here you can view key statistics and navigate to different sections.</p>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Example Statistics Section -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text">{{ $totalUsers ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text">{{ $totalOrders ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Revenue</h5>
                    <p class="card-text">₱{{ $totalRevenue ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Example Recent Activities Section -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent Activities</h5>
                    <ul class="list-group">
                        <li class="list-group-item">Activity 1</li>
                        <li class="list-group-item">Activity 2</li>
                        <li class="list-group-item">Activity 3</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection