<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('locale')) {
            App::setLocale(session()->get('locale'));
        } else {
            $header = $request->server('HTTP_ACCEPT_LANGUAGE');
            if ($header) {
                $codes = collect(explode(',', $header))
                    ->map(fn ($p) => trim(explode(';', $p)[0]))
                    ->filter()
                    ->map(fn ($c) => substr($c, 0, 2))
                    ->unique()
                    ->values();
                if ($codes->isNotEmpty()) {
                    try {
                        $match = \App\Models\Language::whereIn('code', $codes->all())->where('is_active', 1)->first();
                        if ($match) {
                            App::setLocale($match->code);
                            session()->put('locale', $match->code);
                        }
                    } catch (\Throwable $e) {
                        // silent
                        null;
                    }
                }
            }
        }

        return $next($request);
    }
}
