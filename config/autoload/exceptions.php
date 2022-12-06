<?php

declare(strict_types=1);

return [
    'handler' => [
        'http' => [
            Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler::class,
            Mine\Exception\Handler\ValidationExceptionHandler::class,
            Mine\Exception\Handler\TokenExceptionHandler::class,
            Mine\Exception\Handler\NoPermissionExceptionHandler::class,
            Mine\Exception\Handler\NormalStatusExceptionHandler::class,
            Mine\Exception\Handler\AppExceptionHandler::class,
        ],
    ],
];
