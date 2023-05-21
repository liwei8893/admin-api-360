<?php

declare(strict_types=1);

namespace App\Order\Controller\Signup;

use App\Order\Request\OrderSignupRequest;
use App\Order\Service\OrderSignupService;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'order/signup'), Auth]
class OrderSignupController extends MineController
{
    #[Inject]
    public OrderSignupService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('batchAdminSave'), Permission('order:signup:adminSave,users:list:signup'), OperationLog('管理员批量报名')]
    public function batchAdminSave(OrderSignupRequest $request): ResponseInterface
    {
        return $this->success(['status' => $this->service->batchAdminSave($request->all())]);
    }

    /**
     * 报名.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[PostMapping('adminSave'), Permission('order:signup:adminSave,users:list:signup'), OperationLog('管理员报名')]
    public function adminSave(OrderSignupRequest $request): ResponseInterface
    {
        return $this->success(['status' => $this->service->adminSave($request->all())]);
    }
}
