<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KarenderiaController extends Controller
{
    //
    public function showKarenderiaProfile(){
        return view('karenderia.karenderiaProfile');
    }
}
