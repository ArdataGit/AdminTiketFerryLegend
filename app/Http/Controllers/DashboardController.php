<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $menu = 'dashboard';
        return view('admin.dashboard.index', compact('menu'));
    }
}
