<?php

declare(strict_types=1);

namespace App\Sta\Controller\App;

use App\Sta\Request\StaAccessLogRequest;
use App\Sta\Service\StaAccessLogService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'sta/app/access')]
class StaAccessLogController extends MineController
{
    #[Inject]
    protected StaAccessLogService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('setAccessLog')]
    public function setAccessLog(StaAccessLogRequest $request): ResponseInterface
    {
        $this->service->setAccessLog($request->validated());
        return $this->success();
    }
}
