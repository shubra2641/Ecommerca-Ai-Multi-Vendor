<?php

namespace App\Services\AI;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

abstract class BaseAISuggestionService
{
    protected function getSetting(): ?Setting
    {
        return Setting::first();
    }

    protected function validateAI(): ?JsonResponse
    {
        $setting = $this->getSetting();

        if (!$setting?->ai_enabled || $setting?->ai_provider !== 'openai') {
            return response()->json(['error' => 'AI disabled'], 422);
        }

        if (!$setting->ai_openai_api_key) {
            return response()->json(['error' => 'Missing API key'], 422);
        }

        return null;
    }

    protected function getCacheKey(string $title, string $locale, string $type): string
    {
        return "ai_{$type}_v1:" . md5($title . '|' . $locale);
    }

    protected function callOpenAI(string $prompt, string $apiKey): \Illuminate\Http\Client\Response
    {
        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(25)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant. Output concise valid JSON only.'
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

    protected function parseResponse(\Illuminate\Http\Client\Response $response): array
    {
        $rawText = data_get($response->json(), 'choices.0.message.content');

        if (!$rawText) {
            throw new \Exception('Empty response from AI');
        }

        if (preg_match('/\{.*\}/s', $rawText, $matches)) {
            try {
                $parsed = json_decode($matches[0], true, 512, JSON_THROW_ON_ERROR);
                return $this->formatResponse($parsed);
            } catch (\Throwable $e) {
                // Fallback
            }
        }

        return $this->formatResponse([]);
    }

    abstract protected function formatResponse(array $parsed): array;
}
