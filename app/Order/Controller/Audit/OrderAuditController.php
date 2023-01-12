<?php

declare(strict_types=1);

namespace App\Order\Controller\Audit;

use App\Order\Request\OrderRequest;
use App\Order\Service\OrderService;
use App\Order\Service\UsersRenewService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'order/audit'), Auth]
class OrderAuditController extends MineController
{
    #[Inject]
    protected OrderService $orderService;

    #[Inject]
    protected UsersRenewService $renewService;

    /**
     * 获取审核列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('orderList')]
    public function orderList(OrderRequest $request): ResponseInterface
    {
        return $this->success($this->orderService->getAuditList($request->all()));
    }

    public function renewList()
    {
        return $this->success($this->orderService->getPageList());
    }

    public function auditOrder()
    {
        return $this->success($this->orderService->getPageList());
    }

    public function auditRenew()
    {
        return $this->success($this->orderService->getPageList());
    }
}
