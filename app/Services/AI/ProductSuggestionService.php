<?php

namespace App\Services\AI;

use Illuminate\Http\JsonResponse;

class ProductSuggestionService extends BaseAISuggestionService
{
    public function generateSuggestions(string $title, ?string $locale = null): JsonResponse
    {
        if ($error = $this->validateAI()) {
            return $error;
        }

        $locale = $locale ?: app()->getLocale();
        $cacheKey = $this->getCacheKey($title, $locale, 'product');

        if ($cached = cache()->get($cacheKey)) {
            return response()->json($cached + ['cached' => true]);
        }

        try {
            $setting = $this->getSetting();
            $prompt = "Generate JSON with keys description (<=500 chars), short_description (<=200 chars), " .
                "seo_description (<=160 chars), seo_tags (<=12 comma keywords) for product titled '{$title}'. " .
                "Language: {$locale}. Return ONLY JSON.";

            $response = $this->callOpenAI($prompt, $setting->ai_openai_api_key);
            $result = $this->parseResponse($response);

            cache()->put($cacheKey, $result, 600);
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'service_error', 'message' => $e->getMessage()], 502);
        }
    }

    protected function formatResponse(array $parsed): array
    {
        return [
            'description' => mb_substr($parsed['description'] ?? '', 0, 500),
            'short_description' => mb_substr($parsed['short_description'] ?? '', 0, 200),
            'seo_description' => mb_substr($parsed['seo_description'] ?? '', 0, 160),
            'seo_tags' => $parsed['seo_tags'] ?? '',
            'provider_status' => 200,
        ];
    }
}
