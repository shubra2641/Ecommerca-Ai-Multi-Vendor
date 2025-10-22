<?php

return [
    // Stock level thresholds (used for highlighting and filters)
    'stock_low_threshold' => env('STOCK_LOW_THRESHOLD', 5),
    'stock_soon_threshold' => env('STOCK_SOON_THRESHOLD', 15),
    // How many variations to show inline before showing a "+N more" link on mobile/compact views
    'variation_list_limit' => env('VARIATION_LIST_LIMIT', 5),
];
