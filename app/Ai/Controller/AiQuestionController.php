<?php
declare(strict_types=1);

namespace App\Ai\Controller;

use App\Ai\Request\AiQuestionRequest;
use App\Ai\Service\AiQuestionService;
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
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 题目管理控制器
 * Class AiQuestionController
 */
#[Controller(prefix: "ai/question"), Auth]
class AiQuestionController extends MineController
{
    /**
     * 业务处理服务
     * AiQuestionService
     */
    #[Inject]
    protected AiQuestionService $service;


    /**
     * 列表
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("index"), Permission("ai:question, ai:question:index")]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 回收站列表
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("recycle"), Permission("ai:question:recycle")]
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 单个或批量真实删除数据 （清空回收站）
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping("realDelete"), Permission("ai:question:realDelete"), OperationLog]
    public function realDelete(): ResponseInterface
    {
        return $this->service->realDelete((array)$this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的数据
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping("recovery"), Permission("ai:question:recovery"), OperationLog]
    public function recovery(): ResponseInterface
    {
        return $this->service->recovery((array)$this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 新增
     * @param AiQuestionRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("save"), Permission("ai:question:save"), OperationLog]
    public function save(AiQuestionRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新
     * @param int $id
     * @param AiQuestionRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping("update/{id}"), Permission("ai:question:update"), OperationLog]
    public function update(int $id, AiQuestionRequest $request): ResponseInterface
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
    #[GetMapping("read/{id}"), Permission("ai:question:read")]
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
    #[DeleteMapping("delete"), Permission("ai:question:delete"), OperationLog]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array)$this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 更改数据状态
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping("changeStatus"), Permission("ai:question:update"), OperationLog]
    public function changeStatus(): ResponseInterface
    {
        return $this->service->changeStatus(
            (int)$this->request->input('id'),
            (string)$this->request->input('statusValue'),
            (string)$this->request->input('statusName')
        ) ? $this->success() : $this->error();
    }

    /**
     * 更改数据状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeSort'), Permission('ai:question:update')]
    public function changeSort(AiQuestionRequest $request): ResponseInterface
    {
        return $this->service->changeSort($request->all()) ? $this->success() : $this->error();
    }
}
