<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karenderia;

class KarenderiaController extends Controller
{
    //
    public function showKarenderiaProfile(){
        return view('dashboardProfile.karenderiaProfile');
    }

    public function showKarenderiaDashboard()
    {
        $karenderias = Karenderia::select('id', 'name', 'description')
            ->where('status', 'active')
            ->get()
            ->toArray();

        return view('dashboard.karenderiaDash', ['karenderias' => $karenderias]);
    }
}
