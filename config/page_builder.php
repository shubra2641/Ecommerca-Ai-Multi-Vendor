<?php

return [
    'blocks' => [
        'heading' => [
            'label' => 'Heading',
            'fields' => [
                'title' => 'text_multi', // multi-language single line
                'settings.level' => ['type' => 'select', 'options' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], 'default' => 'h2'],
                'settings.align' => ['type' => 'select', 'options' => ['start', 'center', 'end'], 'default' => 'start'],
            ],
        ],
        'text' => [
            'label' => 'Text',
            'fields' => [
                'content' => 'richtext_multi',
            ],
        ],
        'image' => [
            'label' => 'Image',
            'fields' => [
                'settings.url' => ['type' => 'text', 'placeholder' => '/path/or/https://'],
                'settings.alt' => 'text_multi',
                'settings.width' => ['type' => 'number', 'placeholder' => 'optional'],
            ],
        ],
        'button' => [
            'label' => 'Button',
            'fields' => [
                'title' => 'text_multi',
                'settings.url' => ['type' => 'text', 'placeholder' => 'https://'],
                'settings.style' => ['type' => 'select', 'options' => ['primary', 'secondary', 'outline-primary', 'outline-secondary'], 'default' => 'primary'],
            ],
        ],
        'spacer' => [
            'label' => 'Spacer',
            'fields' => [
                'settings.size' => ['type' => 'number', 'default' => 30],
            ],
        ],
        'features_list' => [
            'label' => 'Features List',
            'repeatable' => true,
            'fields' => [
                'content.items' => ['type' => 'repeater', 'schema' => [
                    'icon' => ['type' => 'text', 'placeholder' => 'fa-star'],
                    'title' => ['type' => 'text_multi'],
                    'desc' => ['type' => 'text_multi'],
                ]],
            ],
        ],
        'hero' => [
            'label' => 'Hero Section',
            'fields' => [
                'title' => 'text_multi',
                'content' => 'richtext_multi',
                'settings.bg_image' => ['type' => 'text', 'placeholder' => '/images/hero.jpg'],
                'settings.overlay' => ['type' => 'number', 'default' => 40],
            ],
        ],
        'row' => [
            'label' => 'Row',
            'fields' => [
                'settings.gutter' => ['type' => 'select', 'options' => ['0', '1', '2', '3', '4', '5'], 'default' => '3'],
                'settings.padding_y' => ['type' => 'select', 'options' => ['0', '1', '2', '3', '4', '5'], 'default' => '3'],
                'settings.background_color' => ['type' => 'text', 'placeholder' => '#f8f9fa or rgba(...)'],
                'settings.class' => ['type' => 'text', 'placeholder' => 'additional row classes'],
            ],
        ],
        'column' => [
            'label' => 'Column',
            'fields' => [
                'settings.width_md' => ['type' => 'select', 'options' => ['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], 'default' => '12'],
                'settings.width_lg' => ['type' => 'select', 'options' => ['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], 'default' => '12'],
                'settings.class' => ['type' => 'text', 'placeholder' => 'extra column classes'],
            ],
        ],
        'text_image' => [
            'label' => 'Text + Image',
            'fields' => [
                'title' => 'text_multi',
                'content' => 'richtext_multi',
                'settings.image_url' => ['type' => 'text', 'placeholder' => '/path/image.jpg'],
                'settings.image_alt' => 'text_multi',
                'settings.image_position' => ['type' => 'select', 'options' => ['left', 'right'], 'default' => 'right'],
            ],
        ],
        'image_text' => [
            'label' => 'Image + Text',
            'fields' => [
                'title' => 'text_multi',
                'content' => 'richtext_multi',
                'settings.image_url' => ['type' => 'text', 'placeholder' => '/path/image.jpg'],
                'settings.image_alt' => 'text_multi',
                'settings.image_position' => ['type' => 'select', 'options' => ['left', 'right'], 'default' => 'left'],
            ],
        ],
    ],
];
