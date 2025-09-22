<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white shadow">
        <div class="mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Brand -->
            <div>
                <h1 class="text-2xl font-bold text-blue-700 tracking-tight">KaPlato</h1>
                <p class="text-gray-500 text-sm">Admin Dashboard</p>
            </div>
        </div>
    </header>

    <!-- Sidebar + Main -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r hidden md:flex flex-col p-5 space-y-6">
            <nav>
                <ul class="space-y-3 text-gray-700 font-medium">
                    <li>
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 
                           {{ Route::is('dashboard') ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                           <span>📊</span> Overview
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.karenderia') }}"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 
                           {{ Route::is('dashboard.karenderia') ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                           <span>🏪</span> Karenderia
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.menu') }}"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 
                           {{ Route::is('dashboard.menu') ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                           <span>🍽</span> Meals
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.users') }}"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 
                           {{ Route::is('dashboard.users') ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                           <span>👤</span> Users
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.index') }}"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 
                           {{ Route::is('reports.index') ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                           <span>📝</span> Reports
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.pending') }}"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 
                           {{ Route::is('dashboard.pending') ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                           <span>⏳</span> Pending
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 w-full text-left">
                                <span>🚪</span> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto px-6 py-4 text-center text-sm text-gray-500">
            © {{ date('Y') }} KaPlato. All rights reserved.
        </div>
    </footer>
</body>
</html>
