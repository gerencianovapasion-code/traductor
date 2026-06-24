<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index()
    {
        return view('admin.languages.index', [
            'languages' => Language::orderBy('sort')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Language $language)
    {
        $language->update($request->validate([
            'is_active' => ['nullable', 'boolean'],
            'can_listen' => ['nullable', 'boolean'],
            'can_speak' => ['nullable', 'boolean'],
            'ui' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'integer'],
        ]));

        return back()->with('status', __('messages.saved'));
    }
}
