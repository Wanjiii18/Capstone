<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
  <div class="w-full max-w-5xl bg-white shadow-lg rounded-lg overflow-hidden flex flex-col md:flex-row">
    <!-- Left Side: App Name / Branding -->
    <div class="md:w-1/2 bg-blue-600 flex flex-col justify-center items-center p-8 text-white">
      <h1 class="text-4xl font-extrabold mb-4 tracking-tight">KaPlato</h1>
    </div>

    <!-- Right Side: Login Form -->
    <div class="md:w-1/2 p-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Login to your account</h2>
      <form method="POST" action="{{ route('authenticate') }}" class="space-y-5">
        @csrf
        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                 class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
          @error('email')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <input id="password" name="password" type="password" required
                 class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
          @error('password')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-between">
          <button type="submit"
                  class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg shadow transition">
            Login
          </button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
