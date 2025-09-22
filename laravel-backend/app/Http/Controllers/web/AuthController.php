<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/dashboard'); // Prevent logged-in users from accessing the login page
        }
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email', // Reverted back to 'email'
            'password' => 'required|string|min:8',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Check if the authenticated user is an admin
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Access denied. Only admin users are allowed.', // Reverted error key to 'email'
                ]);
            }

            return redirect()->intended('/dashboard'); // Redirect to dashboard after login
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.', // Reverted error key to 'email'
        ]);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login'); // Redirect to login page after logout
    }

    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect('/dashboard'); // Prevent logged-in users from accessing the registration page
        }
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        Auth::login($user);

        return redirect('/dashboard'); // Redirect to dashboard after registration
    }

    
}
