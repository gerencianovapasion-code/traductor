<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        return view('admin.plans.index', ['plans' => Plan::orderBy('sort')->get()]);
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', ['plan' => $plan]);
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $this->validateData($request);
        $data['features'] = $this->parseFeatures($request->input('features'));
        $plan->update($data);

        return redirect()->route('admin.plans.index')->with('status', __('messages.saved'));
    }

    public function create()
    {
        return view('admin.plans.edit', ['plan' => new Plan(['currency' => 'EUR', 'interval' => 'month', 'level' => 2])]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['features'] = $this->parseFeatures($request->input('features'));
        Plan::create($data);

        return redirect()->route('admin.plans.index')->with('status', __('messages.saved'));
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'slug' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
            'level' => ['required', 'integer', 'min:1', 'max:3'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'interval' => ['required', 'in:month,year,lifetime'],
            'minutes_limit' => ['nullable', 'integer', 'min:0'],
            'engine' => ['required', 'in:browser,cloud'],
            'allow_system_audio' => ['nullable', 'boolean'],
            'ads' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'integer'],
        ]);
    }

    protected function parseFeatures(?string $raw): array
    {
        return collect(explode("\n", (string) $raw))
            ->map(fn ($l) => trim($l))
            ->filter()
            ->values()
            ->all();
    }
}
