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
namespace App\Order\Controller\Sta;

use App\Order\Request\OrderStaRequest;
use App\Order\Service\OrderStaService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 订单管理控制器
 * Class OrderController.
 */
#[Controller(prefix: 'order/sta'), Auth]
class OrderStaController extends MineController
{
    /**
     * 业务处理服务
     * OrderService.
     */
    #[Inject]
    protected OrderStaService $service;

    /**
     * 新增统计表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getNewVipSta')]
    public function getNewVipSta(OrderStaRequest $request): ResponseInterface
    {
        return $this->success($this->service->getNewVipSta($request->all()));
    }

    /**
     * 续费统计表.
     * @param OrderStaRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getRenewalSta')]
    public function getRenewalSta(OrderStaRequest $request): ResponseInterface
    {
        return $this->success($this->service->getRenewalSta($request->all()));
    }

    /**
     * 退费统计表.
     * @param OrderStaRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getRefundSta')]
    public function getRefundSta(OrderStaRequest $request): ResponseInterface
    {
        return $this->success($this->service->getRefundSta($request->all()));
    }
}
