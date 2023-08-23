<?php

declare(strict_types=1);

namespace App\Commerce\Controller\Usage;

use App\Commerce\Service\CommerceCardUsageService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * 电商卡使用记录控制器
 * Class CommerceCardUsageController.
 */
#[Controller(prefix: 'commerce/cardUsage'), Auth]
class CommerceCardUsageController extends MineController
{
    /**
     * 业务处理服务
     * CommerceCardUsageService.
     */
    #[Inject]
    protected CommerceCardUsageService $service;

    /**
     * 列表.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('commerce:cardUsage, commerce:cardUsage:index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 读取数据.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping('read/{id}'), Permission('commerce:cardUsage:read')]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 数据导出.
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping('export'), Permission('commerce:cardUsage:export'), OperationLog]
    public function export(): ResponseInterface
    {
        return $this->service->export($this->request->all(), \App\Commerce\Dto\CommerceCardUsageDto::class, '导出数据列表');
    }
}
