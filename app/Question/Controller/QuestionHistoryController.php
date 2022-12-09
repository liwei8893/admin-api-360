<?php

declare(strict_types=1);

namespace App\Question\Controller;

use App\Question\Dto\QuestionHistoryDto;
use App\Question\Request\QuestionHistoryRequest;
use App\Question\Service\QuestionHistoryService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
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
 * 错题表控制器
 * Class QuestionHistoryController.
 */
#[Controller(prefix: 'question/history'), Auth]
class QuestionHistoryController extends MineController
{
    /**
     * 业务处理服务
     * QuestionHistoryService.
     */
    #[Inject]
    protected QuestionHistoryService $service;

    /**
     * 列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('question:history:index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all(), false));
    }

    /**
     * 新增.
     * @param {CREATE_REQUEST} $request
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save'), Permission('question:history:save'), OperationLog]
    public function save(QuestionHistoryRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新.
     * @param {UPDATE_REQUEST} $request
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('update/{id}'), Permission('question:history:update'), OperationLog]
    public function update(int $id, QuestionHistoryRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 读取数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('read/{id}'), Permission('question:history:read')]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 数据导出.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('export'), Permission('question:history:export'), OperationLog]
    public function export(): ResponseInterface
    {
        return $this->service->bigExport($this->request->all(), QuestionHistoryDto::class, '做题记录');
    }
}
