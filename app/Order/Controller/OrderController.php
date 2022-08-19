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

namespace App\Order\Controller;

use App\Order\Service\OrderService;
use App\Order\Request\OrderRequest;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * 订单管理控制器
 * Class OrderController
 */
#[Controller(prefix: "order"), Auth]
class OrderController extends MineController
{
    /**
     * 业务处理服务
     * OrderService
     */
    #[Inject]
    protected OrderService $service;


    /**
     * 修改有效期
     * @param \App\Order\Request\OrderRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("changeEndDate"), Permission("order:changeEndDate"), OperationLog('修改有效期')]
    public function changeEndDate(OrderRequest $request): ResponseInterface
    {
        return $this->service->changeEndDate($request->all()) ? $this->success() : $this->error();
    }
}