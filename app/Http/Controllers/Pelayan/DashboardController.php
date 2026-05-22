<?php

namespace App\Http\Controllers\Pelayan;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pelayan.dashboard');
    }
}
