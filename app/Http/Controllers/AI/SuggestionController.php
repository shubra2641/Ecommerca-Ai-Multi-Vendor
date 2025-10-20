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
        if (!$request->input('title')) {
            return response()->json(['error' => 'Title required'], 422);
        }
        return $service->generateSuggestions($request->input('title'), $request->input('locale'));
    }

    public function product(Request $request, ProductSuggestionService $service)
    {
        if (!$request->input('title')) {
            return response()->json(['error' => 'Title required'], 422);
        }
        return $service->generateSuggestions($request->input('title'), $request->input('locale'));
    }

    public function category(Request $request, CategorySuggestionService $service)
    {
        if (!$request->input('title')) {
            return response()->json(['error' => 'Title required'], 422);
        }
        return $service->generateSuggestions($request->input('title'), $request->input('locale'));
    }
}
