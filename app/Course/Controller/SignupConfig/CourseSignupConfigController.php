<?php

declare(strict_types=1);

namespace App\Course\Controller\SignupConfig;

use App\Course\Dto\CourseSignupConfigDto;
use App\Course\Request\CourseSignupConfigRequest;
use App\Course\Service\CourseSignupConfigService;
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
 * 课程报名配置表控制器
 * Class CourseSignupConfigController.
 */
#[Controller(prefix: 'course/signupConfig'), Auth]
class CourseSignupConfigController extends MineController
{
    /**
     * 业务处理服务
     * CourseSignupConfigService.
     */
    #[Inject]
    protected CourseSignupConfigService $service;

    /**
     * 列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('course:signupConfig:index,users:list:signup')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all(), false));
    }

    /**
     * 回收站列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('recycle'), Permission('course:signupConfig:recycle')]
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 单个或批量真实删除数据 （清空回收站）.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('realDelete'), Permission('course:signupConfig:realDelete'), OperationLog]
    public function realDelete(): ResponseInterface
    {
        return $this->service->realDelete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('recovery'), Permission('course:signupConfig:recovery'), OperationLog]
    public function recovery(): ResponseInterface
    {
        return $this->service->recovery((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 新增.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save'), Permission('course:signupConfig:save'), OperationLog]
    public function save(CourseSignupConfigRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('update/{id}'), Permission('course:signupConfig:update'), OperationLog]
    public function update(int $id, CourseSignupConfigRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 读取数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('read/{id}'), Permission('course:signupConfig:read')]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 单个或批量删除数据到回收站.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('course:signupConfig:delete'), OperationLog]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 更改数据状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeStatus'), Permission('course:signupConfig:update'), OperationLog]
    public function changeStatus(): ResponseInterface
    {
        return $this->service->changeStatus(
            (int) $this->request->input('title'),
            (string) $this->request->input('statusValue'),
            (string) $this->request->input('statusName')
        ) ? $this->success() : $this->error();
    }

    /**
     * 数字运算操作.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('numberOperation'), Permission('course:signupConfig:update'), OperationLog]
    public function numberOperation(): ResponseInterface
    {
        return $this->service->numberOperation(
            (int) $this->request->input('title'),
            (string) $this->request->input('numberName'),
            (int) $this->request->input('numberValue', 1),
        ) ? $this->success() : $this->error();
    }

    /**
     * 数据导入.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|\PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    #[PostMapping('import'), Permission('course:signupConfig:import')]
    public function import(): ResponseInterface
    {
        return $this->service->import(CourseSignupConfigDto::class) ? $this->success() : $this->error();
    }

    /**
     * 下载导入模板
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('downloadTemplate')]
    public function downloadTemplate(): ResponseInterface
    {
        return (new MineCollection())->export(CourseSignupConfigDto::class, '模板下载', []);
    }

    /**
     * 数据导出.
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('export'), Permission('course:signupConfig:export'), OperationLog]
    public function export(): ResponseInterface
    {
        return $this->service->export($this->request->all(), CourseSignupConfigDto::class, '导出数据列表');
    }
}
