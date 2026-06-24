<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected array $keys = [
        'site_name', 'seo_title', 'seo_description', 'seo_keywords',
        'contact_email', 'analytics_id',
    ];

    public function index()
    {
        $settings = [];
        foreach ($this->keys as $key) {
            $settings[$key] = Setting::get($key);
        }

        return view('admin.settings.index', ['settings' => $settings, 'keys' => $this->keys]);
    }

    public function update(Request $request)
    {
        foreach ($this->keys as $key) {
            Setting::put($key, $request->input($key));
        }

        return back()->with('status', __('messages.saved'));
    }
}
