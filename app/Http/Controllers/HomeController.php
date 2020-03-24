<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function spms (Request $request) {
        return view('spms.dashboard');
    }

    public function plans (Request $request) {
        return view('spms.views.plans');
    }
}
