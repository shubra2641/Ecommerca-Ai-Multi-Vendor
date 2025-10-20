<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\SimpleAIService;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function __construct(private SimpleAIService $ai) {}

    public function blogPost(Request $request)
    {
        return $this->ai->generate($request->input('title', ''), 'blog', $request->input('locale'));
    }

    public function product(Request $request)
    {
        return $this->ai->generate($request->input('title', ''), 'product', $request->input('locale'));
    }

    public function category(Request $request)
    {
        return $this->ai->generate($request->input('title', ''), 'category', $request->input('locale'));
    }
}
