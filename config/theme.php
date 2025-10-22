<?php

return [
    // Core design tokens (buyers can override via config publish or .env-driven mapping later)
    'colors' => [
        'primary' => '#2563eb',
        'primary_dark' => '#1d4ed8',
        'accent' => '#f59e0b',
        'success' => '#16a34a',
        'danger' => '#dc2626',
        'warning' => '#d97706',
        'info' => '#0ea5e9',
        'gray_50' => '#f9fafb',
        'gray_100' => '#f3f4f6',
        'gray_200' => '#e5e7eb',
        'gray_300' => '#d1d5db',
        'gray_600' => '#4b5563',
        'gray_900' => '#111827',
    ],
    'radius' => [
        'sm' => '2px',
        'md' => '4px',
        'lg' => '8px',
        'xl' => '14px',
    ],
    'spacing_scale' => [0, 2, 4, 6, 8, 12, 16, 20, 24, 32],
    'typography' => [
        'base_font' => 'Cairo, system-ui, sans-serif',
        'heading_weight' => 600,
        'body_weight' => 400,
    ],
    'features' => [
        'dark_mode_toggle' => false, // placeholder – can be enabled and wired later
    ],
];
