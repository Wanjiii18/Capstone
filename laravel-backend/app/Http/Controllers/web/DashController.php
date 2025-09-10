<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashController extends Controller
{
    //
    public function showMainDashboard()
    {
        return view('dashboard.mainDash');
    }

    public function showKarenderiaDash(){
        return view('dashboard.karenderiaDash');
    }

    public function showMenuDashboard(){
        return view('dashboard.menuDash');
    }

    public function showUserDashboard(){
        return view('dashboard.userDash');
    }

    public function showReportDashboard(){
        return view('dashboard.reportDash');
    }
}
