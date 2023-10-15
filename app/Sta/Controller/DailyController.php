<?php

declare(strict_types=1);

namespace App\Sta\Controller;

use App\Sta\Service\DailyService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'sta/daily')]
class DailyController extends MineController
{
    #[Inject]
    protected DailyService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getList')]
    public function getList(): ResponseInterface
    {
        return $this->success($this->service->getList($this->request->all()));
    }
}
