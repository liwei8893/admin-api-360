<?php

declare(strict_types=1);

namespace App\Commerce\Controller\Card;

use App\Commerce\Request\CommerceCardRequest;
use App\Commerce\Service\CommerceCardService;
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
 * 电商管理控制器
 * Class CommerceCardController.
 */
#[Controller(prefix: 'commerce/card'), Auth]
class CommerceCardController extends MineController
{
    /**
     * 业务处理服务
     * CommerceCardService.
     */
    #[Inject]
    protected CommerceCardService $service;

    /**
     * 列表.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('commerce:card, commerce:card:index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 回收站列表.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping('recycle'), Permission('commerce:card:recycle')]
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 单个或批量真实删除数据 （清空回收站）.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping('realDelete'), Permission('commerce:card:realDelete'), OperationLog]
    public function realDelete(): ResponseInterface
    {
        return $this->service->realDelete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的数据.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping('recovery'), Permission('commerce:card:recovery'), OperationLog]
    public function recovery(): ResponseInterface
    {
        return $this->service->recovery((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 新增.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping('save'), Permission('commerce:card:save'), OperationLog]
    public function save(CommerceCardRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    #[PostMapping('generateCard'), Permission('commerce:card:save')]
    public function generateCard(CommerceCardRequest $request): ResponseInterface
    {
        return $this->success($this->service->generateCard($request->validated()));
    }

    /**
     * 更新.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping('update/{id}'), Permission('commerce:card:update'), OperationLog]
    public function update(int $id, CommerceCardRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 读取数据.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping('read/{id}'), Permission('commerce:card:read')]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 数据导入.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping('import'), Permission('commerce:card:import')]
    public function import(): ResponseInterface
    {
        return $this->service->import(\App\Commerce\Dto\CommerceCardDto::class) ? $this->success() : $this->error();
    }

    /**
     * 下载导入模板
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping('downloadTemplate')]
    public function downloadTemplate(): ResponseInterface
    {
        return (new \Mine\MineCollection())->export(\App\Commerce\Dto\CommerceCardDto::class, '模板下载', []);
    }

    /**
     * 数据导出.
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping('export'), Permission('commerce:card:export'), OperationLog]
    public function export(): ResponseInterface
    {
        return $this->service->export($this->request->all(), \App\Commerce\Dto\CommerceCardDto::class, '导出数据列表');
    }

    /**
     * 单个或批量删除数据到回收站.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('commerce:card:delete'), OperationLog]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }
}
