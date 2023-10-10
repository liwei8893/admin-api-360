<?php

declare(strict_types=1);

namespace App\Sta\Controller;

use App\Sta\Request\StaAccessLogRequest;
use App\Sta\Service\StaAccessLogService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'sta/access'), Auth]
class StaAccessLogController extends MineController
{
    #[Inject]
    protected StaAccessLogService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getAccessLogMod')]
    public function getAccessLogMod(StaAccessLogRequest $request): ResponseInterface
    {
        return $this->success($this->service->getAccessLogMod($request->validated()));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getAccessLogTotal')]
    public function getAccessLogTotal(StaAccessLogRequest $request): ResponseInterface
    {
        return $this->success($this->service->getAccessLogTotal($request->validated()));
    }
}
