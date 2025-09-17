<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white border-b">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">KaPlato</h1>
                    <p class="text-gray-600">Admin Dashboard</p>
                </div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-md">Generate Report</button>
            </div>
        </div>
    </div>

    <!-- Sidebar and Main Content -->
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-1/12 bg-gray-100 p-4 min-h-screen">
            <ul class="space-y-4">
                <li>
                    <a href="{{ route('dashboard') }}" 
                    class="{{ Route::is('dashboard') ? 'text-blue-600 underline' : 'text-gray-600 hover:text-blue-600' }}">
                    Overview
                    </a>
                </li>
                <li>
                    <a href="{{ route('dashboard.karenderia') }}" 
                    class="{{ Route::is('dashboard.karenderia') ? 'text-blue-600 underline' : 'text-gray-600 hover:text-blue-600' }}">
                    Karenderia
                    </a>
                </li>
                <li>
                    <a href="{{ route('dashboard.menu') }}" 
                    class="{{ Route::is('dashboard.menu') ? 'text-blue-600 underline' : 'text-gray-600 hover:text-blue-600' }}">
                    Meals
                    </a>
                </li>
                <li>
                    <a href="{{ route('dashboard.users') }}" 
                    class="{{ Route::is('dashboard.users') ? 'text-blue-600 underline' : 'text-gray-600 hover:text-blue-600' }}">
                    Users
                    </a>
                </li>
                <li>
                    <a href="{{ route('dashboard.reports') }}" 
                    class="{{ Route::is('dashboard.reports') ? 'text-blue-600 underline' : 'text-gray-600 hover:text-blue-600' }}">
                    Reports
                    </a>
                </li>
                <li>
                    <a href="{{ route('dashboard.pending') }}" 
                    class="{{ Route::is('dashboard.pending') ? 'text-blue-600 underline' : 'text-gray-600 hover:text-blue-600' }}">
                    Pending
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <!-- Stats Cards -->
            @yield('stats')
            <!-- Tabs -->
            @yield('tabs')
            <!-- Main Content -->
            @yield('content')
        </div>
    </div>
</body>
</html>
