<?php

declare(strict_types=1);

namespace App\Order\Controller\Summary;

use App\Order\Dto\OrderSummaryDto;
use App\Order\Request\OrderSummaryRequest;
use App\Order\Service\OrderSummaryService;
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
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 核单记录控制器
 * Class OrderSummaryController.
 */
#[Controller(prefix: 'order/summary'), Auth]
class OrderSummaryController extends MineController
{
    /**
     * 业务处理服务
     * OrderSummaryService.
     */
    #[Inject]
    protected OrderSummaryService $service;

    /**
     * 列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('order:summary, order:summary:index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 回收站列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('recycle'), Permission('order:summary:recycle')]
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 单个或批量真实删除数据 （清空回收站）.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('realDelete'), Permission('order:summary:realDelete'), OperationLog]
    public function realDelete(): ResponseInterface
    {
        return $this->service->realDelete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('recovery'), Permission('order:summary:recovery'), OperationLog]
    public function recovery(): ResponseInterface
    {
        return $this->service->recovery((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 新增.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save'), Permission('order:summary:save'), OperationLog]
    public function save(OrderSummaryRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('update/{id}'), Permission('order:summary:update'), OperationLog]
    public function update(int $id, OrderSummaryRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 读取数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('read/{id}'), Permission('order:summary:read')]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 单个或批量删除数据到回收站.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('order:summary:delete'), OperationLog]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 数据导出.
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('export'), Permission('order:summary:export'), OperationLog]
    public function export(): ResponseInterface
    {
        return $this->service->export($this->request->all(), OrderSummaryDto::class, '核单详情表');
    }
}
