<?php

namespace App\View\Composers;

use Illuminate\View\View;

class AdminBlogPostEditComposer
{
    public function compose(View $view): void
    {
        $post = $view->getData()['post'] ?? null;
        $sel = [];
        if ($post) {
            try {
                $sel = $post->tags->pluck('id')->toArray();
            } catch (\Throwable $e) {
                $sel = [];
            }
        }
        $view->with('blogPostSelectedTags', $sel);
    }
}
