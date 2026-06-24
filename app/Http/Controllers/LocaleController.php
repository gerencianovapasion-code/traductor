<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        if (in_array($locale, config('translator.ui_locales', []), true)) {
            session(['locale' => $locale]);

            if ($request->user()) {
                $request->user()->update(['locale' => $locale]);
            }
        }

        return redirect()->back();
    }
}
