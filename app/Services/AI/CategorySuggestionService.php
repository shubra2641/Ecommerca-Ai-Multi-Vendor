<?php

namespace App\Services\AI;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class CategorySuggestionService
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
        $cacheKey = 'ai_category_v1:' . md5($title . '|' . $locale);

        if ($cached = cache()->get($cacheKey)) {
            return response()->json($cached + ['cached' => true]);
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

    private function callOpenAI(string $title, string $locale, string $apiKey): \Illuminate\Http\Client\Response
    {
        $prompt = "Generate JSON with keys description (<=300 chars), " .
            "seo_description (<=160 chars), seo_tags (<=12 comma keywords) for category titled '{$title}'. " .
            "Language: {$locale}. Return ONLY JSON.";

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(25)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful category assistant. Output concise valid JSON only.'
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

        if (preg_match('/\{.*\}/s', $rawText, $matches)) {
            try {
                $parsed = json_decode($matches[0], true, 512, JSON_THROW_ON_ERROR);
                return [
                    'description' => mb_substr($parsed['description'] ?? '', 0, 300),
                    'seo_description' => mb_substr($parsed['seo_description'] ?? '', 0, 160),
                    'seo_tags' => $parsed['seo_tags'] ?? '',
                    'provider_status' => 200,
                ];
            } catch (\Throwable $e) {
                // Fallback
            }
        }

        return [
            'description' => mb_substr($rawText, 0, 300),
            'seo_description' => mb_substr($rawText, 0, 160),
            'seo_tags' => '',
            'provider_status' => 200,
        ];
    }
}
