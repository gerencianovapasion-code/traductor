<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Text translation + language detection.
 *
 * Speech recognition (STT) and speech synthesis (TTS) happen on the device via
 * the browser Web Speech API. This service only handles the text->text step in
 * the middle, proxied through a configurable provider so we never expose keys
 * to the client and can swap engines per plan.
 */
class TranslationService
{
    public function provider(): string
    {
        return config('translator.provider', 'mymemory');
    }

    /**
     * Translate a chunk of recognised speech.
     *
     * @param  string  $source  source language code or 'auto'
     */
    public function translate(string $text, string $source, string $target): array
    {
        $text = trim($text);

        if ($text === '') {
            return ['text' => '', 'detected' => $source];
        }

        // Same language: nothing to do.
        if ($source !== 'auto' && $this->base($source) === $this->base($target)) {
            return ['text' => $text, 'detected' => $source];
        }

        $cacheKey = 'tr:'.md5($this->provider().'|'.$source.'|'.$target.'|'.$text);

        return Cache::remember($cacheKey, now()->addDay(), function () use ($text, $source, $target) {
            return $this->provider() === 'libretranslate'
                ? $this->viaLibre($text, $source, $target)
                : $this->viaMyMemory($text, $source, $target);
        });
    }

    protected function viaMyMemory(string $text, string $source, string $target): array
    {
        // MyMemory needs an explicit source; fall back to English on 'auto'.
        $src = $source === 'auto' ? 'en' : $this->base($source);
        $cfg = config('translator.mymemory');

        try {
            $res = Http::timeout(8)->get($cfg['endpoint'], array_filter([
                'q' => $text,
                'langpair' => $src.'|'.$this->base($target),
                'de' => $cfg['email'] ?? null,
            ]))->json();

            $out = data_get($res, 'responseData.translatedText');

            return ['text' => $out ?: $text, 'detected' => $src];
        } catch (\Throwable $e) {
            Log::warning('MyMemory translate failed: '.$e->getMessage());

            return ['text' => $text, 'detected' => $src];
        }
    }

    protected function viaLibre(string $text, string $source, string $target): array
    {
        $cfg = config('translator.libretranslate');

        try {
            $res = Http::asForm()->timeout(8)->post(rtrim($cfg['endpoint'], '/').'/translate', array_filter([
                'q' => $text,
                'source' => $source === 'auto' ? 'auto' : $this->base($source),
                'target' => $this->base($target),
                'format' => 'text',
                'api_key' => $cfg['api_key'] ?? null,
            ]))->json();

            return [
                'text' => data_get($res, 'translatedText', $text),
                'detected' => data_get($res, 'detectedLanguage.language', $source),
            ];
        } catch (\Throwable $e) {
            Log::warning('LibreTranslate translate failed: '.$e->getMessage());

            return ['text' => $text, 'detected' => $source];
        }
    }

    /** Detect the language of a text sample (best effort). */
    public function detect(string $text): ?string
    {
        $text = trim($text);
        if ($text === '') {
            return null;
        }

        if ($this->provider() === 'libretranslate') {
            $cfg = config('translator.libretranslate');
            try {
                $res = Http::asForm()->timeout(6)->post(rtrim($cfg['endpoint'], '/').'/detect', array_filter([
                    'q' => $text,
                    'api_key' => $cfg['api_key'] ?? null,
                ]))->json();

                return data_get($res, '0.language');
            } catch (\Throwable $e) {
                Log::warning('LibreTranslate detect failed: '.$e->getMessage());
            }
        }

        return null;
    }

    /** Strip region from a BCP-47 code: pt-BR -> pt (kept for zh-TW / pt-BR which the API distinguishes). */
    protected function base(string $code): string
    {
        $keep = ['zh-TW', 'pt-BR'];

        return in_array($code, $keep, true) ? $code : strtok($code, '-');
    }
}
