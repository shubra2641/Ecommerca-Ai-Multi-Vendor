<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Customization & Theming Hints
    |--------------------------------------------------------------------------
    | This file documents primary extension points for buyers / integrators.
    | It is intentionally light (no runtime logic) and serves as a discoverable
    | map of tweakable layers. You can publish it via:
    |   php artisan vendor:publish --tag=config
    |
    | Values below can be overridden in environment-specific config if needed.
    */

    'cache_ttl' => [
        'settings' => 3600,
        'categories' => 1800,
        'latest_posts' => 900,
        'flash_sale_products' => 300,
        'slides' => 600,
        'banners' => 600,
    ],

    // Enable experimental progressive enhancements (future flags)
    'features' => [
        'hero_slider_autoplay' => true,
        'showcase_brand_section' => true,
    ],

    // Hook placeholders (will be arrays of class@method once hook system implemented)
    'hooks' => [
        'product_card_view_model' => [
            // Example: App\Hooks\AddBadgeToProductCard::class,
        ],
    ],
];
