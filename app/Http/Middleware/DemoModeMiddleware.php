<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DemoModeMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $enabled = (bool) (Config::get('demo.enabled') ?? false);

        if (! $enabled) {
            return $next($request);
        }

        // One-time info banner per session on first GET request
        if ($this->isReadOnlyMethod($request) && ! $request->session()->has('demo_notified')) {
            // Flash once per session; will appear on next navigation
            $request->session()->flash('warning', 'النظام يعمل بوضع العرض (Demo). قد تكون بعض الإجراءات مقفلة.');
            $request->session()->put('demo_notified', true);
        }

        // Allow read-only methods
        if ($this->isReadOnlyMethod($request)) {
            return $next($request);
        }

        // Allow specific routes (login/logout/password/etc)
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;
        if ($routeName && $this->isAllowedRouteName($routeName)) {
            return $next($request);
        }

        // Allow specific API paths if configured
        foreach ((array) Config::get('demo.allow_api_paths', []) as $prefix) {
            if ($prefix !== '' && $request->is($prefix)) {
                return $next($request);
            }
        }

        // Block mutation attempts in demo
        $message = 'النظام في وضع العرض (Demo). تم تعطيل عمليات الإضافة/التعديل/الحذف.';

        if ($request->expectsJson() || $request->is('api/*')) {
            return new JsonResponse(['message' => $message], 403);
        }

        // Redirect back with warning flash; if no referer, go home
        $request->session()->flash('warning', $message);
        $back = (string) $request->headers->get('referer') ?: '/';
        return new RedirectResponse($back);
    }

    private function isReadOnlyMethod(Request $request): bool
    {
        $allowed = (array) Config::get('demo.allow_methods', ['GET', 'HEAD', 'OPTIONS']);
        return in_array($request->getMethod(), $allowed, true);
    }

    private function isAllowedRouteName(string $name): bool
    {
        $patterns = (array) Config::get('demo.allow_route_names', []);
        foreach ($patterns as $pattern) {
            if (Str::is($pattern, $name)) {
                return true;
            }
        }
        return false;
    }
}
