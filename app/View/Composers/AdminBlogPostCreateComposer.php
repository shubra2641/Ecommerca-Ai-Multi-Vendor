<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Language;
use Illuminate\View\View;

class AdminBlogPostCreateComposer
{
    public function compose(View $view): void
    {
        try {
            $languages = Language::where('is_active', 1)->orderByDesc('is_default')->get();
        } catch (\Throwable $e) {
            $languages = collect();
        }
        $default = $languages->firstWhere('is_default', 1) ?? $languages->first();
        $view->with('blogPostLanguages', $languages)->with('blogPostDefaultLanguage', $default);
    }
}
