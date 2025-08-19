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
    
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-medium text-gray-600">Total Users</h2>
            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 12a5 5 0 100-10 5 5 0 000 10zm-7 8a7 7 0 0114 0H3z" />
            </svg>
        </div>
        <p class="text-2xl font-bold">{{ $totalUsers ?? '...' }}</p>
        <p class="text-xs text-green-600">{{ $usersGrowth ?? '' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-medium text-gray-600">Active Carinderias</h2>
            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8 12H6v-2h2v2zm6 0h-2v-2h2v2z" />
            </svg>
        </div>
        <p class="text-2xl font-bold">{{ $activeCarinderias ?? '...' }}</p>
        <p class="text-xs text-green-600">{{ $carinderiasGrowth ?? '' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-medium text-gray-600">Meals Posted Today</h2>
            <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8 12H6v-2h2v2zm6 0h-2v-2h2v2z" />
            </svg>
        </div>
        <p class="text-2xl font-bold">{{ $mealsToday ?? '...' }}</p>
        <p class="text-xs text-orange-600">{{ $mealsGrowth ?? '' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-medium text-gray-600">Avg Rating</h2>
            <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8 12H6v-2h2v2zm6 0h-2v-2h2v2z" />
            </svg>
        </div>
        <p class="text-2xl font-bold">{{ $avgRating ?? '...' }}</p>
        <p class="text-xs text-yellow-600">{{ $avgRatingLabel ?? 'Platform average' }}</p>
    </div>
</div>

    <div class="p-6">
        <!-- Stats Cards -->
        @yield('stats')
        <!-- Tabs -->
        @yield('tabs')
        <!-- Main Content -->
        @yield('content')
    </div>

    
</body>
</html>
