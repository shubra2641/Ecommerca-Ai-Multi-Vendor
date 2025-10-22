<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class HtmlSanitizer
{
    /**
     * Return sanitized HTML for general content.
     */
    public static function sanitize(string $html): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $config->set('HTML.SafeIframe', true);
        $config->set(
            'URI.SafeIframeRegexp',
            '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'
        );
        $config->set(
            'HTML.Allowed',
            'p,b,strong,i,em,a[href|title|rel],ul,ol,li,br,span[style],' .
                'img[src|alt|title|width|height],h1,h2,h3,h4,blockquote'
        );
        $purifier = new HTMLPurifier($config);

        return $purifier->purify($html);
    }

    /**
     * Sanitize embed HTML (allow iframes from known providers).
     */
    public static function sanitizeEmbed(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Allow a minimal set plus safe iframes (YouTube/Vimeo)
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.SafeIframe', true);
        $config->set(
            'URI.SafeIframeRegexp',
            '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'
        );
        $config->set('Attr.AllowedFrameTargets', ['_blank']);

        $purifier = new HTMLPurifier($config);

        return $purifier->purify($html);
    }

    /**
     * Backwards-compatible convenience wrapper used across controllers/tests.
     * Accepts null/empty and returns sanitized string.
     */
    public static function clean(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // If the value looks like an embed (contains iframe), allow embeds
        if (stripos($html, '<iframe') !== false) {
            return static::sanitizeEmbed($html);
        }

        return static::sanitize($html);
    }
}
