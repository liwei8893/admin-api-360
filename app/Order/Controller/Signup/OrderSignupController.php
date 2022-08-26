<?php

namespace App\Order\Controller\Signup;

use App\Course\Request\CourseSignupConfigRequest;
use App\Order\Request\OrderSignupRequest;
use App\Order\Service\OrderSignupService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: "order/signup"), Auth]
class OrderSignupController extends MineController
{
    #[Inject]
    public OrderSignupService $service;

    /**
     * 报名
     * @param \App\Order\Request\OrderSignupRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Exception
     * author:ZQ
     * time:2022-08-26 16:56
     */
    #[PostMapping("adminSave"), Permission("order:signup:adminSave"), OperationLog('管理员报名')]
    public function adminSave(OrderSignupRequest $request): ResponseInterface
    {
        return $this->success(['status' => $this->service->adminSave($request->all())]);
    }
}