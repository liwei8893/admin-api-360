<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Order\Controller;

use App\Order\Request\OrderRequest;
use App\Order\Service\OrderService;
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
     *                                    author:ZQ
     *                                    time:2022-08-21 14:33
     */
    #[PostMapping('changeOrderToUser'), Permission('order:changeOrder'), OperationLog('异动转人')]
    public function changeOrderToUser(OrderRequest $request): ResponseInterface
    {
        return $this->service->changeOrderToUser($request->all()) ? $this->success() : $this->error();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *                                    author:ZQ
     *                                    time:2022-08-21 14:33
     */
    #[PostMapping('changeOrderToCourse'), Permission('order:changeOrder'), OperationLog('异动转班')]
    public function changeOrderToCourse(OrderRequest $request): ResponseInterface
    {
        return $this->service->changeOrderToCourse($request->all()) ? $this->success() : $this->error();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *                                    author:ZQ
     *                                    time:2022-08-21 14:34
     */
    #[PostMapping('changeOrderToRefund'), Permission('order:changeOrder'), OperationLog('异动退费')]
    public function changeOrderToRefund(OrderRequest $request): ResponseInterface
    {
        return $this->service->changeOrderToRefund($request->all()) ? $this->success() : $this->error();
    }
}
