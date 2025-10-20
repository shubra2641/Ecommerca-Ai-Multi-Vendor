<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\BlogPostSuggestionService;
use App\Services\AI\ProductSuggestionService;
use App\Services\AI\CategorySuggestionService;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function blogPost(Request $request, BlogPostSuggestionService $service)
    {
        $title = $request->input('title');
        $locale = $request->input('locale');

        if (!$title) {
            return response()->json(['error' => 'Title required'], 422);
        }

        return $service->generateSuggestions($title, $locale);
    }

    public function product(Request $request, ProductSuggestionService $service)
    {
        $title = $request->input('title');
        $locale = $request->input('locale');

        if (!$title) {
            return response()->json(['error' => 'Title required'], 422);
        }

        return $service->generateSuggestions($title, $locale);
    }

    public function category(Request $request, CategorySuggestionService $service)
    {
        $title = $request->input('title');
        $locale = $request->input('locale');

        if (!$title) {
            return response()->json(['error' => 'Title required'], 422);
        }

        return $service->generateSuggestions($title, $locale);
    }
}
