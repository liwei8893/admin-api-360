<?php
declare(strict_types=1);


namespace App\Crm\Controller;

use App\Crm\Dto\CrmStudyRecordDto;
use App\Crm\Request\CrmStudyRecordRequest;
use App\Crm\Service\CrmStudyRecordService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineCollection;
use Mine\MineController;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 学习记录控制器
 * Class CrmStudyRecordController
 */
#[Controller(prefix: "crm/studyRecord"), Auth]
class CrmStudyRecordController extends MineController
{
    /**
     * 业务处理服务
     * CrmStudyRecordService
     */
    #[Inject]
    protected CrmStudyRecordService $service;

    /**
     * 不分页列表
     * @param CrmStudyRecordRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("list"), Permission("crm:studyRecord, crm:studyRecord:index")]
    public function list(CrmStudyRecordRequest $request): ResponseInterface
    {
        return $this->success($this->service->getList($request->all()));
    }

    /**
     * 列表
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("index"), Permission("crm:studyRecord, crm:studyRecord:index")]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 新增
     * @param CrmStudyRecordRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("save"), Permission("crm:studyRecord:save"), OperationLog]
    public function save(CrmStudyRecordRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 批量新增
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("batchSave"), Permission("crm:studyRecord:save"), OperationLog]
    public function batchSave(): ResponseInterface
    {
        return $this->service->batchSave($this->request->all()) ? $this->success() : $this->error();
    }

    /**
     * 数据导入
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|\PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    #[PostMapping("import"), Permission("crm:studyRecord:import")]
    public function import(): ResponseInterface
    {
        return $this->service->import(CrmStudyRecordDto::class) ? $this->success() : $this->error();
    }

    /**
     * 下载导入模板
     * @return ResponseInterface
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("downloadTemplate")]
    public function downloadTemplate(): ResponseInterface
    {
        return (new MineCollection)->export(CrmStudyRecordDto::class, '模板下载', []);
    }

    /**
     * 更新
     * @param int $id
     * @param CrmStudyRecordRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping("update/{id}"), Permission("crm:studyRecord:update"), OperationLog]
    public function update(int $id, CrmStudyRecordRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 读取数据
     * @param int $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("read/{id}"), Permission("crm:studyRecord:read")]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 单个或批量删除数据到回收站
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping("delete"), Permission("crm:studyRecord:delete"), OperationLog]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array)$this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 数据导出
     * @return ResponseInterface
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("export"), Permission("crm:studyRecord:export"), OperationLog]
    public function export(): ResponseInterface
    {
        return $this->service->export($this->request->all(), CrmStudyRecordDto::class, '导出数据列表');
    }

}
