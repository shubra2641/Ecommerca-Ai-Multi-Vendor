<?php

namespace App\Services\AI;

use App\Models\Setting;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BlogPostSuggestionService
{
    private const CACHE_TTL = 600; // 10 minutes
    private const RATE_LIMIT_TTL = 65; // 65 seconds
    private const RATE_LIMIT_PER_MINUTE = 6;
    private const OPENAI_API_URL = 'https://api.openai.com/v1/chat/completions';
    private const DEFAULT_MODEL = 'gpt-4o-mini';
    private const DEFAULT_TEMPERATURE = 0.65;
    private const REQUEST_TIMEOUT = 25;

    public function generateSuggestions(string $title, ?string $locale = null): JsonResponse
    {
        try {
            $this->validateRequest($title);
            
            $locale = $locale ?: app()->getLocale();
            $cacheKey = $this->buildCacheKey($title, $locale);

            if ($cached = cache()->get($cacheKey)) {
                return $this->successResponse($cached + ['cached' => true, 'source' => 'cache']);
            }

            if ($this->isRateLimited()) {
                return $this->rateLimitResponse();
            }

            $response = $this->makeOpenAiRequest($title, $locale);
            $result = $this->parseAiResponse($response);
            
            cache()->put($cacheKey, $result, self::CACHE_TTL);
            
            return $this->successResponse($result + ['source' => 'live']);
        } catch (\Throwable $e) {
            Log::warning('AI blog post service error: ' . $e->getMessage(), [
                'title' => $title,
                'locale' => $locale,
                'exception' => $e
            ]);
            
            return $this->errorResponse('connection_failed', $e->getMessage(), 502);
        }
    }

    private function validateRequest(string $title): void
    {
        $setting = Setting::first();
        
        if (!$this->isAiEnabled($setting)) {
            throw new \Exception('AI service is disabled');
        }

        if (!$setting->ai_openai_api_key) {
            throw new \Exception('OpenAI API key is missing');
        }
    }

    private function isAiEnabled(?Setting $setting): bool
    {
        return $setting?->ai_enabled && $setting?->ai_provider === 'openai';
    }

    private function buildCacheKey(string $title, string $locale): string
    {
        return 'ai_blog_post_v1:' . md5($title . '|' . $locale);
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

    private function makeOpenAiRequest(string $title, string $locale): Response
    {
        $setting = Setting::first();
        $model = config('services.openai.model', self::DEFAULT_MODEL);
        $prompt = $this->buildPrompt($title, $locale);
        
        $payload = $this->buildRequestPayload($model, $prompt);

        $response = Http::withToken($setting->ai_openai_api_key)
            ->acceptJson()
            ->timeout(self::REQUEST_TIMEOUT)
            ->post(self::OPENAI_API_URL, $payload);

        if (!$response->ok()) {
            $this->handleApiError($response);
        }

        return $response;
    }

    private function buildRequestPayload(string $model, string $prompt): array
    {
        return [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful blogging assistant. Output concise valid JSON only.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => self::DEFAULT_TEMPERATURE,
        ];
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
        
        $errorType = $status === 429 ? 'rate_limited_provider' : 'provider_error';
        
        $errorData = [
            'error' => $errorType,
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
            Log::debug('Failed to parse JSON from AI response', [
                'raw_text' => $rawText,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function formatParsedResponse(array $parsed): array
    {
        return [
            'excerpt' => $this->truncateText((string) ($parsed['excerpt'] ?? ''), 300),
            'body_intro' => trim((string) ($parsed['body_intro'] ?? '')),
            'seo_description' => $this->truncateText((string) ($parsed['seo_description'] ?? ''), 160),
            'seo_tags' => (string) ($parsed['seo_tags'] ?? ''),
            'provider_status' => 200,
        ];
    }

    private function parseFallbackResponse(string $rawText): array
    {
        $lines = preg_split('/\n+/', trim($rawText));
        $result = [
            'excerpt' => '',
            'body_intro' => '',
            'seo_description' => '',
            'seo_tags' => '',
        ];

        foreach ($lines as $line) {
            $line = trim($line);
            
            if ($result['excerpt'] === '' && mb_strlen($line) <= 320) {
                $result['excerpt'] = $line;
                continue;
            }
            
            if ($result['seo_description'] === '' && mb_strlen($line) <= 180) {
                $result['seo_description'] = $line;
                continue;
            }
            
            if ($result['seo_tags'] === '' && str_contains($line, ',')) {
                $result['seo_tags'] = $line;
                continue;
            }
            
            $result['body_intro'] .= $line . "\n\n";
        }

        // Use excerpt as fallback for SEO description
        if ($result['seo_description'] === '' && $result['excerpt'] !== '') {
            $result['seo_description'] = $this->truncateText($result['excerpt'], 160);
        }

        return [
            'excerpt' => $this->truncateText($result['excerpt'], 300),
            'body_intro' => trim($result['body_intro']),
            'seo_description' => $this->truncateText($result['seo_description'], 160),
            'seo_tags' => $result['seo_tags'],
            'provider_status' => 200,
        ];
    }

    private function truncateText(string $text, int $maxLength): string
    {
        return mb_substr($text, 0, $maxLength);
    }

    private function successResponse(array $data): JsonResponse
    {
        return response()->json($data);
    }

    private function errorResponse(string $error, string $message, int $status = 422): JsonResponse
    {
        return response()->json([
            'error' => $error,
            'message' => $message
        ], $status);
    }

    private function rateLimitResponse(): JsonResponse
    {
        return response()->json([
            'error' => 'rate_limited_local',
            'source' => 'local',
            'message' => 'Too many AI requests. Please wait a minute and try again.',
            'retry_after' => 60,
            'limit' => self::RATE_LIMIT_PER_MINUTE,
        ], 429);
    }
}