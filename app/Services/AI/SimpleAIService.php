<?php

namespace App\Services\AI;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SimpleAIService
{
    public function generate(string $title, string $type, ?string $locale = null): JsonResponse
    {
        $setting = Setting::first();
        if (!$setting?->ai_enabled || !$setting?->ai_openai_api_key) {
            return response()->json(['error' => 'AI disabled'], 422);
        }

        $cacheKey = "ai_{$type}_" . md5($title . ($locale ?: app()->getLocale()));
        if ($cached = Cache::get($cacheKey)) {
            return response()->json($cached);
        }

        try {
            $prompt = $this->getPrompt($title, $type, $locale ?: app()->getLocale());
            $response = Http::withToken($setting->ai_openai_api_key)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [['role' => 'user', 'content' => $prompt]]
                ]);

            $result = json_decode($response->json()['choices'][0]['message']['content'] ?? '{}', true) ?: $this->fallback($title, $type);
            Cache::put($cacheKey, $result, 600);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getPrompt(string $title, string $type, string $locale): string
    {
        return match($type) {
            'product' => "JSON for product '{$title}': description (max 500), short_description (max 200), seo_description (max 160), seo_tags (max 12 keywords). Language: {$locale}",
            'category' => "JSON for category '{$title}': description (max 300), seo_description (max 160), seo_tags (max 12 keywords). Language: {$locale}",
            'blog' => "JSON for blog '{$title}': title, content (max 1000), seo_description (max 160), seo_tags (max 12 keywords). Language: {$locale}",
            default => "JSON for '{$title}': description, seo_description, seo_tags. Language: {$locale}"
        };
    }

    private function fallback(string $title, string $type): array
    {
        return match($type) {
            'product' => [
                'description' => "High-quality {$title} with excellent features.",
                'short_description' => "Premium {$title}",
                'seo_description' => "Buy {$title} online",
                'seo_tags' => strtolower($title)
            ],
            'category' => [
                'description' => "Browse our {$title} collection",
                'seo_description' => "{$title} products",
                'seo_tags' => strtolower($title)
            ],
            'blog' => [
                'title' => $title,
                'content' => "Learn about {$title}",
                'seo_description' => "{$title} guide",
                'seo_tags' => strtolower($title)
            ],
            default => ['description' => $title, 'seo_description' => $title, 'seo_tags' => strtolower($title)]
        };
    }
}
