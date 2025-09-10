<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function showUserDashboard()
    {
        $users = \App\Models\User::select('id', 'name', 'email')
            ->get()
            ->toArray();

        return view('dashboard.userDash', ['users' => $users]);
    }

    public function show($id)
    {
        $user = \App\Models\User::findOrFail($id);

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone_number,
            'address' => $user->address,
            'role' => $user->role,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at
        ];

        return view('dashboardProfile.userProfile', ['user' => $data]);
    }
}
