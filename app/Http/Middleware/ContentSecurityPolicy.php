<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request and attach CSP headers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // In local/debug mode we must avoid applying the strict CSP to error pages
        // (HTTP 500+ HTML responses) because the developer error renderer (Whoops /
        // Ignition) relies on inline styles/scripts and dynamic assets; strict CSP
        // blocks those and results in a broken/unstyled error page. Skip enforcing
        // CSP when running in debug and the response is an HTML error response.
        if (config('app.debug')) {
            try {
                $status = $response->getStatusCode();
                $contentType = $response->headers->get('Content-Type') ?? '';
            } catch (\Throwable $e) {
                // If we cannot inspect the response, be conservative and do not add CSP
                return $response;
            }

            if ($status >= 500 && str_contains(strtolower($contentType), 'text/html')) {
                return $response;
            }
        }

        // Adjust domains (fonts, analytics) as needed; keep strict for Envato review.
        // Strict CSP (all inline scripts/styles removed in views; external CDNs should be self-hosted before enabling)
        // If some CDN assets still present (Bootstrap, Chart.js, intl-tel-input)
        // either self-host them under /public or re-add their hosts.
        // build directive parts in arrays to avoid very long literal lines and keep each code line short
        $formActionHosts = [
            "'self'",
            'https://checkout.stripe.com',
            'https://www.sandbox.paypal.com',
            'https://www.paypal.com',
            'https://api.sandbox.paypal.com',
            'https://api.paypal.com',
            'https://payeer.com',
            'https://payeer.com/merchant',
            'https://payeer.com/merchant/checkout',
            'https://checkout.tap.company',
            'https://secure.tap.company',
            'https://tap.company',
            'https://accept.paymob.com',
            'https://localhost',
            'https://127.0.0.1',
        ];

        $imgHosts = [
            "'self'",
            'data:',
            'https://ui-avatars.com',
            'https://10.0.2.2',
            'http://10.0.2.2',
            'https://www.paypalobjects.com',
            'https://www.sandbox.paypal.com',
        ];

        $scriptHosts = [
            "'self'",
            'https://www.paypal.com',
            'https://www.sandbox.paypal.com',
            'https://www.paypalobjects.com',
            'https://js.stripe.com',
            'https://api.tap.company',
            'https://checkout.tap.company',
            'https://secure.tap.company',
            'https://accept.paymob.com',
        ];

        $frameHosts = [
            'https://js.stripe.com',
            'https://hooks.stripe.com',
            'https://www.paypal.com',
            'https://www.sandbox.paypal.com',
            'https://www.paypalobjects.com',
            'https://api.tap.company',
            'https://checkout.tap.company',
            'https://secure.tap.company',
            'https://tap.company',
            'https://accept.paymob.com',
        ];

        $connectHosts = [
            "'self'",
            'https://api.stripe.com',
            'https://api-m.paypal.com',
            'https://api-m.sandbox.paypal.com',
            'https://api.tap.company',
            'https://accept.paymob.com',
            'https://accept.paymob.com/api',
        ];

        $cspEnforced = [
            "default-src 'self'",
            "base-uri 'self'",
            'form-action ' . implode(' ', $formActionHosts),
            'img-src ' . implode(' ', $imgHosts),
            "font-src 'self' data:",
            'script-src ' . implode(' ', $scriptHosts),
            "style-src 'self'",
            "frame-ancestors 'self'",
            'frame-src ' . implode(' ', $frameHosts),
            "object-src 'none'",
            'connect-src ' . implode(' ', $connectHosts),
            'upgrade-insecure-requests',
        ];
        // Report-Only variant (exclude upgrade-insecure-requests to avoid warning)
        $cspReportOnly = array_filter($cspEnforced, fn ($d) => $d !== 'upgrade-insecure-requests');

        $response->headers->set('Content-Security-Policy', implode('; ', $cspEnforced));
        // Reporting endpoints (Report-To & legacy report-uri)
        $reportEndpoint = url('/csp-report');
        $reportTo = [
            'group' => 'csp-endpoint',
            'max_age' => 10800,
            'endpoints' => [['url' => $reportEndpoint]],
            'include_subdomains' => false,
        ];
        $response->headers->set('Report-To', json_encode($reportTo, JSON_UNESCAPED_SLASHES));
        $response->headers->set('Reporting-Endpoints', 'csp-endpoint="' . $reportEndpoint . '"');
        $response->headers->set(
            'Content-Security-Policy-Report-Only',
            implode('; ', $cspReportOnly) . '; report-to csp-endpoint; report-uri ' . $reportEndpoint
        );
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '0'); // Modern browsers rely on CSP

        return $response;
    }
}
