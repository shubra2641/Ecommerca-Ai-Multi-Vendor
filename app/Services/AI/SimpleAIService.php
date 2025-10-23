<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SimpleAIService
{
    public function generate(string $title, string $type, ?string $locale = null): array
    {
        $setting = Setting::first();
        if (! $setting?->ai_enabled) {
            return ['error' => 'AI disabled'];
        }

        // Check if provider and API key are available
        $provider = $setting->ai_provider;
        $apiKey = $setting->ai_openai_api_key;

        if (! $apiKey) {
            return ['error' => 'API key not configured'];
        }

        try {
            $response = $this->makeApiRequest($apiKey, $provider, $title, $type, $locale);

            if (! $response->successful()) {
                $errorMessage = $this->getSpecificErrorMessage($response);
                Log::error('AI Service Error: ' . $errorMessage, ['response' => $response->json()]);

                return ['error' => $errorMessage];
            }

            // Handle different response formats
            if ($provider === 'gemini') {
                $content = $response->json('candidates.0.content.parts.0.text');
            } else {
                $content = $response->json('choices.0.message.content');
            }

            // Clean content from markdown code blocks
            $content = $this->cleanJsonContent($content);

            $result = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($result)) {
                Log::warning('AI returned invalid JSON', ['content' => $content]);

                return ['error' => __('AI returned invalid response. Please try again.')];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('AI Service Exception: ' . $e->getMessage());

            return ['error' => $e->getMessage()];
        }
    }

    private function getSpecificErrorMessage($response): string
    {
        $statusCode = $response->status();
        $responseData = $response->json();

        // Handle specific error cases
        if ($statusCode === 401) {
            return __('Invalid or expired API key. Please check your key in settings.');
        }

        if ($statusCode === 402) {
            return __('Insufficient credits. Please add credits to continue.');
        }

        if ($statusCode === 403) {
            return __('Access denied. Please check your API key permissions.');
        }

        if ($statusCode === 429) {
            return __('Rate limit exceeded. Please try again later.');
        }

        if ($statusCode === 404) {
            return __('Model not found. Please check your API configuration.');
        }

        if ($statusCode === 500) {
            return __('Server error. Please try again later.');
        }

        // Check for specific error messages in response
        if (isset($responseData['error'])) {
            $error = $responseData['error'];

            // OpenAI specific errors
            if (isset($error['type'])) {
                switch ($error['type']) {
                    case 'insufficient_quota':
                        return __('Insufficient credits. Please add credits to continue.');
                    case 'invalid_api_key':
                        return __('Invalid API key. Please check your key in settings.');
                    case 'rate_limit_exceeded':
                        return __('Rate limit exceeded. Please try again later.');
                }
            }

            // Gemini specific errors
            if (isset($error['code']) && $error['code'] === 400) {
                if (isset($error['message']) && str_contains(strtolower($error['message']), 'api key not valid')) {
                    return __('Invalid API key. Please check your key in settings.');
                }
            }

            // Grok specific errors
            if (isset($error['error']) && str_contains($error['error'], 'credits')) {
                return __('Insufficient credits. Please add credits to continue.');
            }
            if (isset($error['error']) && str_contains($error['error'], 'permission')) {
                return __('Access denied. Please check your API key permissions.');
            }

            // Check error message content
            if (isset($error['message'])) {
                $message = strtolower($error['message']);

                if (str_contains($message, 'insufficient') || str_contains($message, 'quota') || str_contains($message, 'credit')) {
                    return __('Insufficient credits. Please add credits to continue.');
                }

                if (str_contains($message, 'invalid') && str_contains($message, 'key')) {
                    return __('Invalid API key. Please check your key in settings.');
                }

                if (str_contains($message, 'rate') || str_contains($message, 'limit')) {
                    return __('Rate limit exceeded. Please try again later.');
                }
            }
        }

        // Default error message
        return __('AI service error. Please try again later.');
    }

    private function makeApiRequest($apiKey, $provider, $title, $type, $locale = null)
    {
        $config = $this->getApiConfig($provider);

        // Handle Gemini API differently
        if ($provider === 'gemini') {
            return Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-goog-api-key' => $apiKey,
                ])
                ->post($config['endpoint'], [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => 'You are a professional content writer. Always return valid JSON format only. ' . $this->getPrompt($title, $type, $locale),
                                ],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 1000,
                    ],
                ]);
        }

        // Handle OpenAI and Grok APIs
        return Http::withToken($apiKey)
            ->timeout(30)
            ->post($config['endpoint'], [
                'model' => $config['model'],
                'temperature' => 0.7,
                'max_tokens' => 1000,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a professional content writer. Always return valid JSON format only.'],
                    ['role' => 'user', 'content' => $this->getPrompt($title, $type, $locale)],
                ],
            ]);
    }

    private function getApiConfig($provider): array
    {
        return match ($provider) {
            'openai' => [
                'endpoint' => 'https://api.openai.com/v1/chat/completions',
                'model' => 'gpt-4o-mini',
            ],
            'grok' => [
                'endpoint' => 'https://api.x.ai/v1/chat/completions',
                'model' => 'grok-4-latest',
            ],
            'gemini' => [
                'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent',
                'model' => 'gemini-2.0-flash',
            ],
            default => [
                'endpoint' => 'https://api.openai.com/v1/chat/completions',
                'model' => 'gpt-4o-mini',
            ],
        };
    }

    private function getPrompt(string $title, string $type, ?string $locale = null): string
    {
        $language = $this->getLanguageName($locale);
        $languageInstruction = $language ? "Write in {$language} language. " : '';

        return match ($type) {
            'product' => "{$languageInstruction}Create detailed content for product '{$title}'. Return JSON with: description (max 500 chars), short_description (max 200 chars), seo_description (max 160 chars), seo_tags (comma separated keywords). Make it professional and appealing.",
            'category' => "{$languageInstruction}Create content for product category '{$title}'. Return JSON with: description (max 300 chars), seo_title (max 60 chars), seo_description (max 160 chars), seo_tags (comma separated keywords). Make it informative and SEO-friendly.",
            'blog' => "{$languageInstruction}Create blog content about '{$title}'. Return JSON with: content (max 1000 chars), seo_description (max 160 chars), seo_tags (comma separated keywords). Make it engaging and informative.",
            default => "{$languageInstruction}Create content for '{$title}'. Return JSON with: description, seo_description, seo_tags. Make it professional."
        };
    }

    private function getLanguageName(?string $locale = null): string
    {
        if (! $locale) {
            return '';
        }

        return match ($locale) {
            'ar' => 'Arabic',
            'en' => 'English',
            'fr' => 'French',
            'de' => 'German',
            'es' => 'Spanish',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            default => ''
        };
    }

    private function cleanJsonContent(string $content): string
    {
        // Remove markdown code blocks
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);

        // Remove any leading/trailing whitespace
        return trim($content);
    }
}
