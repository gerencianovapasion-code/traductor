<?php

namespace App\Console\Commands;

use App\Services\TranslationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncTranslations extends Command
{
    protected $signature = 'translations:sync {locale? : Target locale, or all UI locales when omitted} {--base=en : Source locale to translate from} {--force : Overwrite existing keys}';

    protected $description = 'Auto-translate the UI strings (messages.php) into the configured locales using the translation engine.';

    public function handle(TranslationService $translator): int
    {
        $base = $this->option('base');
        $basePath = lang_path("$base/messages.php");

        if (! File::exists($basePath)) {
            $this->error("Base locale file not found: $basePath");

            return self::FAILURE;
        }

        $source = require $basePath;
        $targets = $this->argument('locale')
            ? [$this->argument('locale')]
            : array_diff(config('translator.ui_locales', []), [$base]);

        foreach ($targets as $locale) {
            $this->translateLocale($translator, $locale, $base, $source);
        }

        $this->info('Done. Run "php artisan optimize:clear" if config is cached.');

        return self::SUCCESS;
    }

    protected function translateLocale(TranslationService $translator, string $locale, string $base, array $source): void
    {
        $path = lang_path("$locale/messages.php");
        $existing = File::exists($path) ? require $path : [];
        $force = $this->option('force');
        $out = $existing;

        $this->line("→ $locale");
        $bar = $this->output->createProgressBar(count($source));

        foreach ($source as $key => $value) {
            if (! $force && isset($existing[$key])) {
                $bar->advance();
                continue;
            }

            // Protect :placeholders so the engine doesn't translate them.
            preg_match_all('/:\w+/', $value, $m);
            $protected = $value;
            foreach ($m[0] as $i => $ph) {
                $protected = str_replace($ph, "XPLH{$i}X", $protected);
            }

            $res = $translator->translate($protected, $base, $locale);
            $text = $res['text'] ?? $value;

            foreach ($m[0] as $i => $ph) {
                $text = preg_replace('/XPLH'.$i.'X/i', $ph, $text);
            }

            $out[$key] = $text;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if (! File::isDirectory(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }

        File::put($path, "<?php\n\nreturn ".var_export($out, true).";\n");
    }
}
