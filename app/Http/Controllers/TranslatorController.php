<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\TranslationSession;
use App\Services\TranslationService;
use Illuminate\Http\Request;

class TranslatorController extends Controller
{
    public function __construct(protected TranslationService $translator) {}

    public function index(Request $request)
    {
        $plan = $request->user()?->currentPlan();

        return view('translator', [
            'languages' => Language::active()->get(),
            'plan' => $plan,
            'usedMinutes' => $this->minutesUsedThisMonth($request),
        ]);
    }

    /** POST /api/translate — translate a recognised speech fragment. */
    public function translate(Request $request)
    {
        $data = $request->validate([
            'text' => ['required', 'string', 'max:5000'],
            'source' => ['required', 'string', 'max:12'],
            'target' => ['required', 'string', 'max:12'],
        ]);

        $result = $this->translator->translate($data['text'], $data['source'], $data['target']);

        return response()->json($result);
    }

    /** POST /api/detect — best-effort language detection of a text sample. */
    public function detect(Request $request)
    {
        $data = $request->validate(['text' => ['required', 'string', 'max:2000']]);

        return response()->json(['language' => $this->translator->detect($data['text'])]);
    }

    /** POST /api/usage — log a finished translation segment (for quotas/stats). */
    public function logUsage(Request $request)
    {
        $data = $request->validate([
            'source' => ['nullable', 'string', 'max:12'],
            'target' => ['required', 'string', 'max:12'],
            'seconds' => ['required', 'integer', 'min:0', 'max:86400'],
            'characters' => ['nullable', 'integer', 'min:0'],
            'engine' => ['nullable', 'string', 'max:16'],
        ]);

        TranslationSession::create([
            'user_id' => $request->user()?->id,
            'source_lang' => $data['source'] ?? null,
            'target_lang' => $data['target'],
            'engine' => $data['engine'] ?? 'browser',
            'seconds' => $data['seconds'],
            'characters' => $data['characters'] ?? 0,
        ]);

        $limit = $request->user()?->currentPlan()->minutes_limit;
        $used = $this->minutesUsedThisMonth($request);

        return response()->json([
            'used_minutes' => $used,
            'limit_minutes' => $limit,
            'over_limit' => $limit !== null && $used >= $limit,
        ]);
    }

    protected function minutesUsedThisMonth(Request $request): int
    {
        if (! $request->user()) {
            return 0;
        }

        $seconds = TranslationSession::where('user_id', $request->user()->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('seconds');

        return (int) ceil($seconds / 60);
    }
}
