<?php

return [
    // Fields that are allowed to contain HTML (be careful adding to this list)
    'allow_html_fields' => [
        'body',
        'description',
        'extra_html',
        'pb_content',
    ],

    // Maximum length for text inputs to avoid very large payloads
    'max_length' => 65535,
];
