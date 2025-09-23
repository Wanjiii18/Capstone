<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Emergency login route for presentation
Route::post('/emergency-login', function (Request $request) {
    // For demonstration - use alica@gmail.com directly
    $user = User::where('email', 'alica@gmail.com')->first();
    
    if ($user && $user->role === 'karenderia_owner') {
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'displayName' => $user->name,
                'role' => $user->role,
                'verified' => $user->verified
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
            'karenderia' => [
                'id' => $user->karenderia->id,
                'business_name' => $user->karenderia->business_name,
                'status' => $user->karenderia->status,
                'approved_at' => $user->karenderia->approved_at->format('M d, Y')
            ]
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
          ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
    
    return response()->json(['message' => 'User not found'], 404);
});