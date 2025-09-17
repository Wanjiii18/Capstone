<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karenderia;

class PendingController extends Controller
{
    //
    public function showPendingDashboard()
    {
        $pendingKarenderias = Karenderia::select('id', 'name', 'description')
            ->where('status', 'pending')
            ->get()
            ->toArray();

        return view('dashboard.pendingDashboard', ['pendingKarenderias' => $pendingKarenderias]);
    }

}
