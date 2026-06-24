<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Plan;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'plans' => Plan::where('is_active', true)->orderBy('sort')->get(),
            'languageCount' => Language::active()->count(),
            'languages' => Language::active()->take(24)->get(),
        ]);
    }
}
