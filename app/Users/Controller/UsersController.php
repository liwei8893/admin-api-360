<?php

namespace App\Users\Controller;

use App\Users\Request\UsersRequest;
use App\Users\Service\UsersService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: "users"), Auth]
class UsersController extends MineController
{
    #[Inject]
    protected UsersService $service;

    /**
     * @param \App\Users\Request\UsersRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * author:ZQ
     * time:2022-08-28 11:33
     */
    #[PostMapping("batchChangePlatform"), Permission("users:batchChangePlatform"), OperationLog('批量更改平台')]
    public function batchChangePlatform(UsersRequest $request): ResponseInterface
    {
        return $this->success($this->service->batchChangePlatform($request->all()));
    }

    /**
     * 更换手机号
     * @param \App\Users\Request\UsersRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * author:ZQ
     * time:2022-08-28 14:49
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("changeMobile"), Permission("users:changeMobile"), OperationLog('更换手机号')]
    public function changeMobile(UsersRequest $request): ResponseInterface
    {
        return $this->service->changeMobile($request->all()) ? $this->success() : $this->error();
    }
}