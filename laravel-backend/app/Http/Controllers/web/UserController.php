<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function showUserDashboard(Request $request)
    {
        $sort = $request->query('sort', 'name'); // Default sort by name
        $users = User::select('id', 'name', 'email')
            ->orderBy($sort)
            ->paginate(10); // Paginate with 10 users per page

        return view('user.userDash', ['users' => $users]);
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

        return view('user.userProfile', ['user' => $data]);
    }
}
