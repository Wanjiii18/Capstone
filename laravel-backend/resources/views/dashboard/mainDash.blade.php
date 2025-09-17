@extends('dashboard.dashLayout')


@section('content')
<div class="container-fluid">
    <!-- KPIs Section -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text">{{ $totalUsers ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Active Users</h5>
                    <p class="card-text">{{ $activeUsers ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text">{{ $totalOrders ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Reports</h5>
                    <p class="card-text">{{ $totalReports ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphs Section -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">User Growth</h5>
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Karenderia Stats</h5>
                    <canvas id="karenderiaStatsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Section -->
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Users',
                data: [50, 100, 150, 200, 250, 300],
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            }
        }
    });

    // Karenderia Stats Chart
    const karenderiaStatsCtx = document.getElementById('karenderiaStatsChart').getContext('2d');
    new Chart(karenderiaStatsCtx, {
        type: 'bar',
        data: {
            labels: ['Active', 'Pending', 'Inactive'],
            datasets: [{
                label: 'Karenderias',
                data: [20, 5, 3],
                backgroundColor: ['#4caf50', '#ff9800', '#f44336'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            }
        }
    });
</script>
@endsection