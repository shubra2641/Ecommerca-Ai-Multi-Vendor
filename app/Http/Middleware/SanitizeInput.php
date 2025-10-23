<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $input = $request->all();
            $allowHtml = config('sanitizer.allow_html_fields', []);
            array_walk_recursive($input, function (&$value, $key) use ($allowHtml): void {
                if (! is_string($value)) {
                    return;
                }
                // Trim and remove control characters
                $value = trim(preg_replace('/[\x00-\x1F\x7F]/u', '', $value));
                // Enforce max length
                $max = config('sanitizer.max_length', 65535);
                if (mb_strlen($value) > $max) {
                    $value = mb_substr($value, 0, $max);
                }
                // If field is not allowed to contain HTML, strip tags
                if (! in_array($key, $allowHtml, true)) {
                    $value = strip_tags($value);
                }
            });

            // Replace request input (preserve files)
            $request->replace($input);
        }

        return $next($request);
    }
}
