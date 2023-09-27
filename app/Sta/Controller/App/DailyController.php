<?php

declare(strict_types=1);

namespace App\Sta\Controller\App;

use App\Sta\Service\DailyService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'sta/app')]
class DailyController extends MineController
{
    #[Inject]
    protected DailyService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('setDailyHits')]
    public function setDailyHits(): ResponseInterface
    {
        $this->service->setDailyHits();
        return $this->success();
    }
}
