<?php

declare(strict_types=1);

namespace App\Users\Controller;

use App\Users\Request\UsersRequest;
use App\Users\Service\UsersService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'users'), Auth]
class UsersController extends MineController
{
    #[Inject]
    protected UsersService $service;

    /**
     * author:ZQ
     * time:2022-08-28 11:33.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('batchChangePlatform'), Permission('users:batchChangePlatform'), OperationLog('批量更改平台')]
    public function batchChangePlatform(UsersRequest $request): ResponseInterface
    {
        return $this->success($this->service->batchChangePlatform($request->all()));
    }

    /**
     * 更换手机号.
     * author:ZQ
     * time:2022-08-28 14:49.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('changeMobile'), Permission('users:changeMobile'), OperationLog('更换手机号')]
    public function changeMobile(UsersRequest $request): ResponseInterface
    {
        return $this->service->changeMobile($request->all()) ? $this->success() : $this->error();
    }

    /**
     * 检测手机号是否存在.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('hasMobile/{mobile}')]
    public function hasMobile(string $mobile): ResponseInterface
    {
        return $this->success(['status' => $this->service->hasMobile($mobile)]);
    }
}
