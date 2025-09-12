@extends('dashboard.dashLayout')


@section('content')
<div class="container-fluid">
    <div class="row">
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