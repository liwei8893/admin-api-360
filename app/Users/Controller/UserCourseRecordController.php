<?php

declare(strict_types=1);

namespace App\Users\Controller;

use App\Users\Dto\UserCourseRecordDto;
use App\Users\Service\UserCourseRecordService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 听课记录控制器
 * Class UserCourseRecordController.
 */
#[Controller(prefix: 'users/userCourseRecord'), Auth]
class UserCourseRecordController extends MineController
{
    /**
     * 业务处理服务
     * UserCourseRecordService.
     */
    #[Inject]
    protected UserCourseRecordService $service;

    /**
     * 列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('users:userCourseRecord:index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all(), false));
    }

    /**
     * 读取数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('read/{id}'), Permission('users:userCourseRecord:read')]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 数据导出.
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('export'), Permission('users:userCourseRecord:export'), OperationLog]
    public function export(): ResponseInterface
    {
        return $this->service->bigExport($this->request->all(), UserCourseRecordDto::class, '听课记录');
    }
}
