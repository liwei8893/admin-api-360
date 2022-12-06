<?php

declare(strict_types=1);
return [
    'scan' => [
        'paths' => [
            BASE_PATH . '/app',
            BASE_PATH . '/mine',
            BASE_PATH . '/api',
        ],
        'ignore_annotations' => [
            'mixin',
            'required',
        ],
    ],
];
