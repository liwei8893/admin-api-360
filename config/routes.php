<?php

declare(strict_types=1);

use App\System\Controller\ServerController;
use App\System\Middleware\WsAuthMiddleware;
use Hyperf\HttpServer\Router\Router;

Router::get('/', function () {
    return 'welcome use mineAdmin';
});

Router::get('/favicon.ico', function () {
    return '';
});

// 消息ws服务器
Router::addServer('message', function () {
    Router::get('/message.io', ServerController::class, [
        'middleware' => [WsAuthMiddleware::class],
    ]);
});
