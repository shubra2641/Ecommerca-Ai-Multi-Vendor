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
            $system = $this->getSystemInstruction($type);
            $response = Http::withToken($setting->ai_openai_api_key)
                ->asJson()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'temperature' => 0.3,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => $system],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if ($response->failed()) {
                $msg = $response->json('error.message') ?: 'AI request failed';
                return response()->json(['error' => $msg], 502);
            }

            $content = $response->json('choices.0.message.content');
            $parsed = is_array($content) ? $content : json_decode((string)$content, true);
            $result = is_array($parsed) && ! empty($parsed)
                ? $parsed
                : $this->fallback($title, $type);

            Cache::put($cacheKey, $result, 600);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getPrompt(string $title, string $type, string $locale): string
    {
        return match ($type) {
            'product' => "Generate content for product '{$title}' in {$locale}.",
            'category' => "Generate content for product category '{$title}' in {$locale}.",
            'blog' => "Generate content for blog '{$title}' in {$locale}.",
            default => "Generate content for '{$title}' in {$locale}."
        };
    }

    private function getSystemInstruction(string $type): string
    {
        // Force strict JSON schema without Markdown
        return match ($type) {
            'product' => 'Return ONLY a JSON object with keys: description (<=500 chars), short_description (<=200 chars), seo_description (<=160 chars), seo_tags (array of up to 12 keywords). No markdown, no extra text.',
            'category' => 'Return ONLY a JSON object with keys: description (<=300 chars), seo_description (<=160 chars), seo_tags (array of up to 12 keywords). No markdown, no extra text.',
            'blog' => 'Return ONLY a JSON object with keys: title, content (<=1000 chars), seo_description (<=160 chars), seo_tags (array of up to 12 keywords). No markdown, no extra text.',
            default => 'Return ONLY a JSON object with keys: description, seo_description, seo_tags. No markdown, no extra text.',
        };
    }

    private function fallback(string $title, string $type): array
    {
        return match ($type) {
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
