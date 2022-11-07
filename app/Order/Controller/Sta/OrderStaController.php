<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\Order\Controller\Sta;

use App\Order\Dto\Sta\OrderStaByPlatformDto;
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
 * Class OrderController
 */
#[Controller(prefix: "order/sta"), Auth]
class OrderStaController extends MineController
{
    /**
     * 业务处理服务
     * OrderService
     */
    #[Inject]
    protected OrderStaService $service;

    /**
     * @param OrderStaRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getStaByPlatform')]
    public function getStaByPlatform(OrderStaRequest $request): ResponseInterface
    {
        return $this->success($this->service->getStaByPlatform($request->all()));
    }

    /**
     *
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function platformExport(): ResponseInterface
    {
        $params = $this->request->all();
        return $this->service->bigExport($params, OrderStaByPlatformDto::class, '优课会员新增统计表');
    }
}