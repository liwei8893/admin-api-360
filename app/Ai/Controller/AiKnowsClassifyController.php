<?php
declare(strict_types=1);

namespace App\Ai\Controller;

use App\Ai\Request\AiKnowsClassifyRequest;
use App\Ai\Service\AiKnowsClassifyService;
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
 * 知识点分类控制器
 * Class AiKnowsClassifyController
 */
#[Controller(prefix: "ai/knowsClassify"), Auth]
class AiKnowsClassifyController extends MineController
{
    /**
     * 业务处理服务
     * AiKnowsClassifyService
     */
    #[Inject]
    protected AiKnowsClassifyService $service;


    /**
     * 获取列表树
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("index"), Permission("ai:knowsClassify, ai:knowsClassify:index")]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getTreeList($this->request->all()));
    }

    /**
     * 前端选择树（不需要权限）
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("tree")]
    public function tree(): ResponseInterface
    {
        return $this->success($this->service->getSelectTree());
    }

    /**
     * 新增
     * @param AiKnowsClassifyRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("save"), Permission("ai:knowsClassify:save"), OperationLog]
    public function save(AiKnowsClassifyRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新
     * @param int $id
     * @param AiKnowsClassifyRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping("update/{id}"), Permission("ai:knowsClassify:update"), OperationLog]
    public function update(int $id, AiKnowsClassifyRequest $request): ResponseInterface
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
    #[GetMapping("read/{id}"), Permission("ai:knowsClassify:read")]
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
    #[DeleteMapping("delete"), Permission("ai:knowsClassify:delete"), OperationLog]
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
    #[PutMapping("changeStatus"), Permission("ai:knowsClassify:update"), OperationLog]
    public function changeStatus(): ResponseInterface
    {
        return $this->service->changeStatus(
            (int)$this->request->input('id'),
            (string)$this->request->input('statusValue'),
            (string)$this->request->input('statusName')
        ) ? $this->success() : $this->error();
    }

}
