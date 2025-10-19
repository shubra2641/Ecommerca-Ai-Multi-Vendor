<?php

namespace App\Services\AI;

use App\Models\Setting;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BlogPostSuggestionService
{
    private const CACHE_TTL = 600; // 10 minutes
    private const RATE_LIMIT_TTL = 65; // 65 seconds
    private const RATE_LIMIT_PER_MINUTE = 6;

    public function generateSuggestions(string $title, ?string $locale = null): \Illuminate\Http\JsonResponse
    {
        $setting = Setting::first();
        
        if (!$this->isAiEnabled($setting)) {
            return response()->json(['error' => 'AI disabled'], 422);
        }

        if (!$setting->ai_openai_api_key) {
            return response()->json(['error' => 'Missing API key'], 422);
        }

        $locale = $locale ?: app()->getLocale();
        $cacheKey = 'ai_blog_post_v1:' . md5($title . '|' . $locale);

        if ($cached = cache()->get($cacheKey)) {
            return response()->json($cached + ['cached' => true, 'source' => 'cache']);
        }

        if ($this->isRateLimited()) {
            return $this->getRateLimitResponse();
        }

        try {
            $response = $this->makeOpenAiRequest($title, $locale, $setting->ai_openai_api_key);
            $result = $this->parseAiResponse($response);
            
            cache()->put($cacheKey, $result, self::CACHE_TTL);
            
            return response()->json($result + ['source' => 'live']);
        } catch (\Throwable $e) {
            Log::warning('AI blog post HTTP exception: ' . $e->getMessage());
            return response()->json(['error' => 'connection_failed', 'message' => $e->getMessage()], 502);
        }
    }

    private function isAiEnabled(?Setting $setting): bool
    {
        return $setting?->ai_enabled && $setting?->ai_provider === 'openai';
    }

    private function isRateLimited(): bool
    {
        $userId = auth()->id() ?: 0;
        $rateKey = 'ai_blog_post_rate:' . $userId . ':' . now()->format('YmdHi');
        
        $count = cache()->increment($rateKey);
        
        if ($count === 1) {
            cache()->put($rateKey, 1, self::RATE_LIMIT_TTL);
        }

        return $count > self::RATE_LIMIT_PER_MINUTE;
    }

    private function getRateLimitResponse(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'error' => 'rate_limited_local',
            'source' => 'local',
            'message' => 'Too many AI requests. Please wait a minute and try again.',
            'retry_after' => 60,
            'limit' => self::RATE_LIMIT_PER_MINUTE,
        ], 429);
    }

    private function makeOpenAiRequest(string $title, string $locale, string $apiKey): Response
    {
        $model = config('services.openai.model', 'gpt-4o-mini');
        $prompt = $this->buildPrompt($title, $locale);
        
        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful blogging assistant. Output concise valid JSON only.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.65,
        ];

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(25)
            ->post('https://api.openai.com/v1/chat/completions', $payload);

        if (!$response->ok()) {
            $this->handleApiError($response);
        }

        return $response;
    }

    private function buildPrompt(string $title, string $locale): string
    {
        return sprintf(
            "Generate JSON with keys excerpt (<=300 chars engaging summary), body_intro (2 paragraphs opening), seo_description (<=160 chars), seo_tags (<=12 comma keywords) for a blog post titled '%s'. Language: %s. Return ONLY JSON.",
            $title,
            $locale
        );
    }

    private function handleApiError(Response $response): void
    {
        $status = $response->status();
        $body = $response->json();
        
        $errorData = [
            'error' => $status === 429 ? 'rate_limited_provider' : 'provider_error',
            'source' => 'provider',
            'provider_status' => $status,
            'provider_body' => $body,
            'provider_message' => data_get($body, 'error.message'),
            'retry_after' => $response->header('Retry-After') ? (int) $response->header('Retry-After') : null,
        ];

        throw new \Exception(json_encode($errorData), $status);
    }

    private function parseAiResponse(Response $response): array
    {
        $rawText = data_get($response->json(), 'choices.0.message.content');
        
        if (!$rawText) {
            throw new \Exception('Empty output from AI service');
        }

        $parsed = $this->extractJsonFromResponse($rawText);
        
        if (is_array($parsed)) {
            return $this->formatParsedResponse($parsed);
        }

        return $this->parseFallbackResponse($rawText);
    }

    private function extractJsonFromResponse(string $rawText): ?array
    {
        if (!preg_match('/\{.*\}/s', $rawText, $matches)) {
            return null;
        }

        try {
            return json_decode($matches[0], true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function formatParsedResponse(array $parsed): array
    {
        return [
            'excerpt' => mb_substr((string) ($parsed['excerpt'] ?? ''), 0, 300),
            'body_intro' => trim((string) ($parsed['body_intro'] ?? '')),
            'seo_description' => mb_substr((string) ($parsed['seo_description'] ?? ''), 0, 160),
            'seo_tags' => (string) ($parsed['seo_tags'] ?? ''),
            'provider_status' => 200,
        ];
    }

    private function parseFallbackResponse(string $rawText): array
    {
        $lines = preg_split('/\n+/', trim($rawText));
        $excerpt = '';
        $bodyIntro = '';
        $seoDescription = '';
        $seoTags = '';

        foreach ($lines as $line) {
            $line = trim($line);
            
            if ($excerpt === '' && mb_strlen($line) <= 320) {
                $excerpt = $line;
                continue;
            }
            
            if ($seoDescription === '' && mb_strlen($line) <= 180) {
                $seoDescription = $line;
                continue;
            }
            
            if ($seoTags === '' && str_contains($line, ',')) {
                $seoTags = $line;
                continue;
            }
            
            $bodyIntro .= $line . "\n\n";
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
