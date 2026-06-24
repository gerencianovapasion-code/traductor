<?php

namespace App\Http\Controllers;

use App\Models\Plan;

class PageController extends Controller
{
    public function pricing()
    {
        return view('pricing', [
            'plans' => Plan::where('is_active', true)->orderBy('sort')->get(),
        ]);
    }

    public function features()
    {
        return view('features');
    }

    public function legal(string $doc)
    {
        abort_unless(in_array($doc, ['privacy', 'terms', 'cookies'], true), 404);

        return view('legal.'.$doc);
    }
}
