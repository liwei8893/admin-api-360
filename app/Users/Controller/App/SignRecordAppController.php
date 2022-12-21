<?php

declare(strict_types=1);

namespace App\Users\Controller\App;

use App\Users\Service\SignRecordAppService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'users/app/sign')]
class SignRecordAppController extends MineController
{
    #[Inject]
    protected SignRecordAppService $service;

    /**
     * 是否签到.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('hasSign'),Auth('app')]
    public function hasSign(): ResponseInterface
    {
        return $this->success(['status' => $this->service->hasSign()]);
    }

    /**
     * 签到.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('signing'),Auth('app')]
    public function signing(): ResponseInterface
    {
        return $this->success($this->service->signing());
    }
}
