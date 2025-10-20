<?php

namespace App\Services\AI;

use App\Services\AI\SimpleAIService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AIFormHelper
{
    public function __construct(private SimpleAIService $aiService) {}

    /**
     * Handle AI generation for forms with flash messages and input merging
     */
    public function handleFormGeneration(Request $request, string $type, array $config = []): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|min:3',
            'locale' => 'nullable|string|max:10',
            'target' => 'nullable|string',
        ]);

        // Get title from various sources
        $title = $this->extractTitle($request, $validated, $config);

        if (!is_string($title) || mb_strlen(trim($title)) < 3) {
            return back()->with('error', __('AI generation failed: missing or too short title'))->withInput();
        }

        // Call AI service
        $response = $this->aiService->generate($title, $type, $validated['locale'] ?? null);
        $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;
        $payload = method_exists($response, 'getData') ? $response->getData(true) : [];

        if ($status >= 400 || isset($payload['error'])) {
            $reason = is_array($payload) && isset($payload['error']) ? $payload['error'] : __('Unknown error');
            return back()->with('error', __('AI generation failed: :reason', ['reason' => $reason]))->withInput();
        }

        // Merge generated content based on target
        $merge = $this->mergeGeneratedContent($payload, $validated, $config);

        return back()->with('success', __('AI content generated successfully'))->withInput($merge);
    }

    /**
     * Extract title from request based on configuration
     */
    private function extractTitle(Request $request, array $validated, array $config): ?string
    {
        // Direct title from request
        if (!empty($validated['title'])) {
            return $validated['title'];
        }

        $fallback = config('app.fallback_locale');
        $locale = $validated['locale'] ?? $fallback;

        // Try locale-specific name field
        if ($request->has("name.$locale")) {
            $name = $request->input("name.$locale");
            if (is_string($name) && !empty(trim($name))) {
                return $name;
            }
        }

        // Try title field for blog posts
        if ($request->has("title.$locale")) {
            $title = $request->input("title.$locale");
            if (is_string($title) && !empty(trim($title))) {
                return $title;
            }
        }

        // Try default locale
        if ($request->has("name.$fallback")) {
            $name = $request->input("name.$fallback");
            if (is_string($name) && !empty(trim($name))) {
                return $name;
            }
        }

        // Try simple name field
        if ($request->has('name') && is_string($request->input('name'))) {
            return $request->input('name');
        }

        // Try simple title field
        if ($request->has('title') && is_string($request->input('title'))) {
            return $request->input('title');
        }

        return null;
    }

    /**
     * Merge generated content into form inputs
     */
    private function mergeGeneratedContent(array $payload, array $validated, array $config): array
    {
        $merge = [];
        $target = $validated['target'] ?? 'description';
        $locale = $validated['locale'] ?? config('app.fallback_locale');

        switch ($target) {
            case 'base':
            case 'description':
                if (!empty($payload['description'])) {
                    $merge['description'] = [$locale => $payload['description']];
                }
                break;

            case 'short':
                if (!empty($payload['short_description'])) {
                    $merge['short_description'] = [$locale => $payload['short_description']];
                }
                break;

            case 'seo':
                if (!empty($payload['seo_description'])) {
                    $merge['seo_description'] = [$locale => $payload['seo_description']];
                }
                if (!empty($payload['seo_tags'])) {
                    $seoTags = is_array($payload['seo_tags'])
                        ? implode(',', $payload['seo_tags'])
                        : $payload['seo_tags'];
                    $merge['seo_keywords'] = [$locale => $seoTags];
                }
                break;

            case 'i18n':
                if (!empty($payload['description'])) {
                    $merge['description_i18n'] = [$locale => $payload['description']];
                }
                break;

            case 'excerpt':
                if (!empty($payload['content'])) {
                    $excerpt = mb_substr(strip_tags($payload['content']), 0, 300);
                    $merge['excerpt'] = [$locale => $excerpt];
                }
                break;

            case 'body':
                if (!empty($payload['content'])) {
                    $merge['body'] = [$locale => $payload['content']];
                }
                break;

            case 'blog_seo':
                if (!empty($payload['seo_description'])) {
                    $merge['seo_description'] = [$locale => $payload['seo_description']];
                }
                if (!empty($payload['seo_tags'])) {
                    $seoTags = is_array($payload['seo_tags'])
                        ? implode(',', $payload['seo_tags'])
                        : $payload['seo_tags'];
                    $merge['seo_tags'] = [$locale => $seoTags];
                }
                break;
        }

        return $merge;
    }
}
