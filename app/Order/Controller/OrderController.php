<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Order\Request\OrderRequest;
use App\Order\Service\OrderService;
use App\Order\Service\UsersRenewService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 订单管理控制器
 * Class OrderController.
 */
#[Controller(prefix: 'order'), Auth]
class OrderController extends MineController
{
    /**
     * 业务处理服务
     * OrderService.
     */
    #[Inject]
    protected OrderService $service;

    #[Inject]
    protected UsersRenewService $usersRenewService;

    /**
     * 修改有效期
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('changeEndDate'), Permission('order:changeEndDate'), OperationLog('修改有效期')]
    public function changeEndDate(OrderRequest $request): ResponseInterface
    {
        return $this->service->changeEndDate($request->all()) ? $this->success() : $this->error();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('changeOrderToUser'), Permission('order:changeOrder'), OperationLog('异动转人')]
    public function changeOrderToUser(OrderRequest $request): ResponseInterface
    {
        return $this->service->changeOrderToUser($request->all()) ? $this->success() : $this->error();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('changeOrderToCourse'), Permission('order:changeOrder'), OperationLog('异动转班')]
    public function changeOrderToCourse(OrderRequest $request): ResponseInterface
    {
        return $this->service->changeOrderToCourse($request->all()) ? $this->success() : $this->error();
    }

    /**
     * 批量退费.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('batchChangeOrderToRefund'), Permission('order:changeOrder'), OperationLog('异动退费')]
    public function batchChangeOrderToRefund(OrderRequest $request): ResponseInterface
    {
        return $this->service->batchChangeOrderToRefund($request->all()) ? $this->success() : $this->error();
    }

    /**
     * 退费.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('changeOrderToRefund'), Permission('order:changeOrder'), OperationLog('异动退费')]
    public function changeOrderToRefund(OrderRequest $request): ResponseInterface
    {
        return $this->service->changeOrderToRefund($request->all()) ? $this->success() : $this->error();
    }

    /**
     * 订单恢复正常状态.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('changeOrderToNormal'), Permission('order:changeOrder'), OperationLog('异动恢复订单状态')]
    public function changeOrderToNormal(OrderRequest $request): ResponseInterface
    {
        return $this->service->changeOrderToNormal($request->all()) ? $this->success() : $this->error();
    }

    /**
     * 暂停订单.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('changeOrderToPause'), Permission('order:changeOrder'), OperationLog('异动暂停')]
    public function changeOrderToPause(OrderRequest $request): ResponseInterface
    {
        return $this->service->changeOrderToPause($request->all()) ? $this->success() : $this->error();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('changeOrderToDelete'), Permission('order:delete'), OperationLog('异动删除')]
    public function changeOrderToDelete(OrderRequest $request): ResponseInterface
    {
        return $this->service->changeOrderToDelete($request->all()) ? $this->success() : $this->error();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('editOrder'), Permission('order:editOrder'), OperationLog('异动编辑')]
    public function editOrder(OrderRequest $request): ResponseInterface
    {
        return $this->service->editOrder($request->all()) ? $this->success() : $this->error();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('editRenew'), Permission('order:editRenew'), OperationLog('异动编辑续费')]
    public function editRenew(OrderRequest $request): ResponseInterface
    {
        return $this->usersRenewService->editRenew($request->all()) ? $this->success() : $this->error();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('deleteRenew'), Permission('order:deleteRenew'), OperationLog('异动删除续费')]
    public function deleteRenew(OrderRequest $request): ResponseInterface
    {
        return $this->usersRenewService->deleteRenew($request->all()) ? $this->success() : $this->error();
    }
}
