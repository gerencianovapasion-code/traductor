<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

class SeoController extends Controller
{
    public function sitemap()
    {
        $locales = config('translator.ui_locales', ['en']);
        $routes = ['/', '/pricing', '/features', '/translate'];

        $urls = [];
        foreach ($routes as $path) {
            foreach ($locales as $locale) {
                $urls[] = [
                    'loc' => url($path).'?lang='.$locale,
                    'alternates' => array_map(fn ($l) => [
                        'hreflang' => $l,
                        'href' => url($path).'?lang='.$l,
                    ], $locales),
                ];
            }
        }

        return Response::view('seo.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /dashboard',
            'Sitemap: '.url('/sitemap.xml'),
        ]);

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }
}
