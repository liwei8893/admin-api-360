<?php


declare(strict_types=1);

namespace Mine;

use Hyperf\HttpServer\Server;

class MineServer extends Server
{
    protected ?string $serverName = 'MineAdmin';

    protected $routes;

    public function onRequest($request, $response): void
    {
        parent::onRequest($request, $response);
        $this->bootstrap();
    }

    /**
     * MineServer bootstrap.
     */
    protected function bootstrap(): void
    {
    }
}
