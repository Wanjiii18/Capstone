@extends('dashboard.dashLayout')


@section('content')
<div class="modern-admin-dashboard p-6 space-y-6 bg-gray-100 min-h-screen animate-fade-in">
    <!-- Modern Welcome Header -->
    <!-- <div class="modern-welcome-section bg-gradient-to-r from-indigo-500 to-blue-500 rounded-2xl p-6 text-white shadow-lg flex items-center gap-4">
        <div class="admin-avatar bg-white bg-opacity-20 p-4 rounded-full">
            <ion-icon name="person" class="text-4xl"></ion-icon>
        </div>
        <div>
            <h1 class="text-2xl font-bold">Welcome, Admin</h1>
            <p class="text-sm opacity-80">KaPlato Administrative Dashboard</p>
        </div>
    </div> -->


    <!-- Modern Stats Grid -->
    <div class="modern-stats-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="stat-card bg-white rounded-xl shadow hover:shadow-md transition-shadow p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="stat-icon text-blue-500 text-3xl">
                    <ion-icon name="people"></ion-icon>
                </div>
                    <div class="text-sm font-medium text-gray-500">Total Users</div>
                </div>
            <div class="stat-value text-3xl font-bold text-gray-800">{{ $totalUsers ?? 'N/A' }}</div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow hover:shadow-md transition-shadow p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="stat-icon text-blue-500 text-3xl">
                    <ion-icon name="people"></ion-icon>
                </div>
                    <div class="text-sm font-medium text-gray-500">Active Users</div>
                </div>
            <div class="stat-value text-3xl font-bold text-gray-800">{{ $activeUsers ?? 'N/A' }}</div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow hover:shadow-md transition-shadow p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="stat-icon text-blue-500 text-3xl">
                    <ion-icon name="people"></ion-icon>
                </div>
                    <div class="text-sm font-medium text-gray-500">Total Orders</div>
                </div>
            <div class="stat-value text-3xl font-bold text-gray-800">{{ $totalOrders ?? 'N/A' }}</div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow hover:shadow-md transition-shadow p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="stat-icon text-blue-500 text-3xl">
                    <ion-icon name="people"></ion-icon>
                </div>
                    <div class="text-sm font-medium text-gray-500">Total Reports</div>
                </div>
            <div class="stat-value text-3xl font-bold text-gray-800">{{ $totalReports ?? 'N/A' }}</div>
        </div>
    </div>

    <!-- Modern Graphs Section -->
    <div class="modern-section-card bg-white rounded-xl shadow p-6">
        <div class="section-header flex items-center gap-3 mb-4">
            <ion-icon name="analytics" class="text-blue-500 text-2xl"></ion-icon>
            <div>
                <h2 class="text-lg font-semibold text-gray-800">User Growth</h2>
                <p class="text-sm text-gray-500">Track user growth over time</p>
            </div>
        </div>
        <div class="section-content">
            <canvas id="userGrowthChart" class="w-full"></canvas>
        </div>
    </div>

    <div class="modern-section-card">
        <div class="section-header">
            <div class="header-info">
                <div class="header-icon">
                    <ion-icon name="stats-chart" color="secondary"></ion-icon>
                </div>
                <div class="header-text">
                    <h2>Karenderia Stats</h2>
                    <p>Overview of karenderia statuses</p>
                </div>
            </div>
        </div>
        <div class="section-content">
            <canvas id="karenderiaStatsChart"></canvas>
        </div>
    </div>

    <!-- Modern Recent Activities -->
    <div class="modern-section-card bg-white rounded-xl shadow p-6">
        <div class="section-header flex items-center gap-3 mb-4">
            <ion-icon name="list" class="text-green-500 text-2xl"></ion-icon>
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Recent Activities</h2>
                <p class="text-sm text-gray-500">Latest actions in the system</p>
            </div>
        </div>
        <ul class="space-y-2 text-gray-700">
            <li class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">Activity 1</li>
            <li class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">Activity 2</li>
            <li class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">Activity 3</li>
        </ul>
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