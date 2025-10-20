<?php

namespace App\Services\AI;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BlogPostSuggestionService
{
    public function generateSuggestions(string $title, ?string $locale = null): JsonResponse
    {
        $setting = Setting::first();

        if (!$setting?->ai_enabled || $setting?->ai_provider !== 'openai') {
            return response()->json(['error' => 'AI disabled'], 422);
        }

        if (!$setting->ai_openai_api_key) {
            return response()->json(['error' => 'Missing API key'], 422);
        }

        $locale = $locale ?: app()->getLocale();
        $cacheKey = 'ai_blog_post_v1:' . md5($title . '|' . $locale);

        if ($cached = cache()->get($cacheKey)) {
            return response()->json($cached + ['cached' => true]);
        }

        if ($this->isRateLimited()) {
            return response()->json([
                'error' => 'rate_limited',
                'message' => 'Too many requests. Please wait a minute.',
            ], 429);
        }

        try {
            $response = $this->callOpenAI($title, $locale, $setting->ai_openai_api_key);
            $result = $this->parseResponse($response);

            cache()->put($cacheKey, $result, 600);
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'service_error', 'message' => $e->getMessage()], 502);
        }
    }

    private function isRateLimited(): bool
    {
        $userId = auth()->id() ?: 0;
        $rateKey = 'ai_blog_post_rate:' . $userId . ':' . now()->format('YmdHi');

        $count = cache()->increment($rateKey);
        if ($count === 1) {
            cache()->put($rateKey, 1, 65);
        }

        return $count > 6;
    }

    private function callOpenAI(string $title, string $locale, string $apiKey): \Illuminate\Http\Client\Response
    {
        $prompt = "Generate JSON with keys excerpt (<=300 chars), body_intro (2 paragraphs), " .
            "seo_description (<=160 chars), seo_tags (<=12 comma keywords) for blog post titled '{$title}'. " .
            "Language: {$locale}. Return ONLY JSON.";

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(25)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful blogging assistant. Output concise valid JSON only.'
                    ],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.65,
            ]);

        if (!$response->ok()) {
            throw new \Exception('OpenAI API error: ' . $response->status());
        }

        return $response;
    }

    private function parseResponse(\Illuminate\Http\Client\Response $response): array
    {
        $rawText = data_get($response->json(), 'choices.0.message.content');

        if (!$rawText) {
            throw new \Exception('Empty response from AI');
        }

        // Try to extract JSON
        if (preg_match('/\{.*\}/s', $rawText, $matches)) {
            try {
                $parsed = json_decode($matches[0], true, 512, JSON_THROW_ON_ERROR);
                return [
                    'excerpt' => mb_substr($parsed['excerpt'] ?? '', 0, 300),
                    'body_intro' => trim($parsed['body_intro'] ?? ''),
                    'seo_description' => mb_substr($parsed['seo_description'] ?? '', 0, 160),
                    'seo_tags' => $parsed['seo_tags'] ?? '',
                    'provider_status' => 200,
                ];
            } catch (\Throwable $e) {
                // Fall through to fallback parsing
            }
        }

        // Fallback: parse line by line
        $lines = preg_split('/\n+/', trim($rawText));
        $excerpt = '';
        $bodyIntro = '';
        $seoDescription = '';
        $seoTags = '';

        foreach ($lines as $line) {
            $line = trim($line);

            if ($excerpt === '' && mb_strlen($line) <= 320) {
                $excerpt = $line;
            } elseif ($seoDescription === '' && mb_strlen($line) <= 180) {
                $seoDescription = $line;
            } elseif ($seoTags === '' && str_contains($line, ',')) {
                $seoTags = $line;
            } else {
                $bodyIntro .= $line . "\n\n";
            }
        }

        if ($seoDescription === '' && $excerpt !== '') {
            $seoDescription = mb_substr($excerpt, 0, 160);
        }

        return [
            'excerpt' => mb_substr($excerpt, 0, 300),
            'body_intro' => trim($bodyIntro),
            'seo_description' => mb_substr($seoDescription, 0, 160),
            'seo_tags' => $seoTags,
            'provider_status' => 200,
        ];
    }
}
