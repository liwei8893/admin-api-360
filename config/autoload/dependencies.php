<?php

declare(strict_types=1);
return [
    Hyperf\Database\Commands\Ast\ModelUpdateVisitor::class => Mine\MineModelVisitor::class,
    Hyperf\HttpServer\CoreMiddleware::class => Mine\Middlewares\HttpCoreMiddleware::class,
    Mine\Interfaces\UserServiceInterface::class => App\System\Service\Dependencies\UserAuthService::class,
];
