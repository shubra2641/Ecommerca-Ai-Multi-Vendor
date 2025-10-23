<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SimpleAIService
{
    public function generate(string $title, string $type): array
    {
        $setting = Setting::first();
        if (! $setting?->ai_enabled || ! $setting?->ai_openai_api_key) {
            return ['error' => 'AI disabled'];
        }

        try {
            $response = Http::withToken($setting->ai_openai_api_key)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'temperature' => 0.7,
                    'max_tokens' => 1000,
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a professional content writer. Always return valid JSON format only.'],
                        ['role' => 'user', 'content' => $this->getPrompt($title, $type)],
                    ],
                ]);

            if (! $response->successful()) {
                $error = $response->json('error.message') ?: 'HTTP ' . $response->status();
                Log::error('AI Service Error: ' . $error, ['response' => $response->json()]);

                return ['error' => 'AI service error: ' . $error];
            }

            $content = $response->json('choices.0.message.content');
            $result = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($result)) {
                Log::warning('AI returned invalid JSON', ['content' => $content]);

                return $this->getFallback($title, $type);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('AI Service Exception: ' . $e->getMessage());

            return ['error' => $e->getMessage()];
        }
    }

    private function getPrompt(string $title, string $type): string
    {
        return match ($type) {
            'product' => "Create detailed content for product '{$title}'. Return JSON with: description (max 500 chars), short_description (max 200 chars), seo_description (max 160 chars), seo_tags (comma separated keywords). Make it professional and appealing.",
            'category' => "Create content for product category '{$title}'. Return JSON with: description (max 300 chars), seo_description (max 160 chars), seo_tags (comma separated keywords). Make it informative and SEO-friendly.",
            'blog' => "Create blog content about '{$title}'. Return JSON with: content (max 1000 chars), seo_description (max 160 chars), seo_tags (comma separated keywords). Make it engaging and informative.",
            default => "Create content for '{$title}'. Return JSON with: description, seo_description, seo_tags. Make it professional."
        };
    }

    private function getFallback(string $title, string $type): array
    {
        return match ($type) {
            'product' => [
                'description' => "High-quality {$title}",
                'short_description' => "Premium {$title}",
                'seo_description' => "Buy {$title} online",
                'seo_tags' => strtolower($title),
            ],
            'category' => [
                'description' => "Browse our {$title} collection",
                'seo_description' => "{$title} products",
                'seo_tags' => strtolower($title),
            ],
            'blog' => [
                'content' => "Learn about {$title}",
                'seo_description' => "{$title} guide",
                'seo_tags' => strtolower($title),
            ],
            default => [
                'description' => $title,
                'seo_description' => $title,
                'seo_tags' => strtolower($title),
            ]
        };
    }
}
