<?php

declare(strict_types=1);

namespace App\Question\Controller\App;

use App\Question\Request\QuestionAppRequest;
use App\Question\Service\QuestionHistoryService;
use App\Question\Service\QuestionService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 题库管理控制器
 * Class QuestionController.
 */
#[Controller(prefix: 'question/app')]
class QuestionAppController extends MineController
{
    /**
     * 业务处理服务
     * QuestionService.
     */
    #[Inject]
    protected QuestionHistoryService $historyService;

    #[Inject]
    protected QuestionService $questionService;

    /**
     * 做题记录.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getUserQuestion'),Auth('app')]
    public function getUserQuestion(QuestionAppRequest $request): ResponseInterface
    {
        return $this->success($this->questionService->getUserQuestion($request->all()));
    }

    /**
     * 交换题目收藏状态
     * @param QuestionAppRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('changeErrorCollect'),Auth('app')]
    public function changeErrorCollect(QuestionAppRequest $request): ResponseInterface
    {
        return $this->historyService->changeErrorCollect($request->all()) ? $this->success() : $this->error();
    }

    /**
     * 获取做题排行榜.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getRanking')]
    public function getRanking(): ResponseInterface
    {
        return $this->success($this->historyService->getRanking());
    }

    /**
     * 获取我的做题排名.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getRankingMe'),Auth('app')]
    public function getRankingMe(): ResponseInterface
    {
        return $this->success($this->historyService->getRankingMe());
    }

    /**
     * 获取题目综合报告图表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getReport'),Auth('app')]
    public function getReport(): ResponseInterface
    {
        return $this->success($this->historyService->getReport());
    }
}
