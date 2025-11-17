<?php
declare(strict_types=1);


namespace App\Users\Controller;

use App\Users\Service\UserDownloadFileLogService;
use App\Users\Request\UserDownloadFileLogRequest;
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
 * 用户下载文件记录控制器
 * Class UserDownloadFileLogController
 */
#[Controller(prefix: "users/userDownloadFileLog"), Auth]
class UserDownloadFileLogController extends MineController
{
    /**
     * 业务处理服务
     * UserDownloadFileLogService
     */
    #[Inject]
    protected UserDownloadFileLogService $service;

    
    /**
     * 列表
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("index"), Permission("users:userDownloadFileLog, users:userDownloadFileLog:index")]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 读取数据
     * @param int $id
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("read/{id}"), Permission("users:userDownloadFileLog:read")]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 数据导出
     * @return ResponseInterface
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("export"), Permission("users:userDownloadFileLog:export"), OperationLog]
    public function export(): ResponseInterface
    {
        return $this->service->export($this->request->all(), \App\Users\Dto\UserDownloadFileLogDto::class, '导出数据列表');
    }

}
