<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Preset
    |--------------------------------------------------------------------------
    |
    | This option controls the default preset that will be used by PHP Insights
    | to make your code reliable, simple, and clean. You may set this to
    | any of the presets defined in the "presets" array below.
    |
    */
    'preset' => 'laravel',

    /*
    |--------------------------------------------------------------------------
    | IDE
    |--------------------------------------------------------------------------
    |
    | This options allow to add hyperlinks in your terminal to quickly open
    | files in your favorite IDE while browsing your PhpInsights report.
    |
    */
    'ide' => 'vscode',

    /*
    |--------------------------------------------------------------------------
    | Config
    |--------------------------------------------------------------------------
    |
    | Here you may adjust the configs so that they are perfectly fitted to your
    | project.
    |
    */
    'exclude' => [
        'bootstrap/cache',
        'storage',
        'vendor',
        'node_modules',
        'public',
        'database/migrations',
        'database/seeders',
        'database/factories',
        'tests',
    ],

    'add' => [
        //  ExampleMetric::class => [
        //      ExampleInsight::class,
        //  ]
    ],

    'remove' => [
        //  ExampleInsight::class,
    ],

    'config' => [
        //  ExampleInsight::class => [
        //      'key' => 'value',
        //  ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Requirements
    |--------------------------------------------------------------------------
    |
    | Here you may define the level of strictness your project should follow.
    | If you set them to false, it means your project is allowed to not
    | follow the rule.
    |
    */
    'requirements' => [
        'min-quality' => 80,
        'min-complexity' => 80,
        'min-architecture' => 80,
        'min-style' => 80,
        'disable-security-check' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Threads
    |--------------------------------------------------------------------------
    |
    | Here you may adjust how many threads (cores) PHP Insights will use to
    | perform the analysis. This will globally override the number of cores
    | to be used by the parallel processing.
    |
    */
    'threads' => null,
];
