<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $uiLocales = config('translator.ui_locales', []);
        $sort = 0;

        foreach (config('translator.languages', []) as $lang) {
            Language::updateOrCreate(
                ['code' => $lang['code']],
                [
                    'name' => $lang['name'],
                    'native_name' => $lang['native'] ?? $lang['name'],
                    'flag' => $lang['flag'] ?? null,
                    'speech_code' => $lang['speech'] ?? null,
                    'can_listen' => true,
                    'can_speak' => true,
                    'ui' => ($lang['ui'] ?? false) || in_array($lang['code'], $uiLocales, true),
                    'is_active' => true,
                    'sort' => $sort++,
                ]
            );
        }
    }
}
