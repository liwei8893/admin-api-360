<?php

declare(strict_types=1);

use Mine\Middlewares\CorsMiddleware;

return [
    'http' => [
        CorsMiddleware::class,
        \Hyperf\Validation\Middleware\ValidationMiddleware::class,
        \Mine\Middlewares\CheckModuleMiddleware::class,
    ],
];
