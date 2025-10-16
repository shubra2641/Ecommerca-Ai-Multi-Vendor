<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $content = Cache::remember('sitemap.xml', 600, function () {
            $urls = [];
            // Homepage
            $urls[] = [
                'loc' => url('/'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ];
            $xml = view('sitemap.xml', compact('urls'))->render();

            return $xml;
        });

        return Response::make($content, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
