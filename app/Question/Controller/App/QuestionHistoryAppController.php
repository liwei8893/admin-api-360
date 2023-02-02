<?php

declare(strict_types=1);

namespace App\Question\Controller\App;

use App\Question\Dto\QuestionHistoryDto;
use App\Question\Request\QuestionHistoryRequest;
use App\Question\Service\QuestionHistoryService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use JsonException;
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
#[Controller(prefix: 'question/app/history'), Auth]
class QuestionHistoryAppController extends MineController
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    #[PostMapping('submit')]
    public function submit(QuestionHistoryRequest $request): ResponseInterface
    {
        return $this->success($this->service->submit($request->all()));
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
