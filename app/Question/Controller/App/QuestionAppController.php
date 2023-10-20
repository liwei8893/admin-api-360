<?php

declare(strict_types=1);

namespace App\Question\Controller\App;

use App\Question\Request\QuestionAppRequest;
use App\Question\Service\QuestionAppService;
use App\Question\Service\QuestionHistoryService;
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
    protected QuestionAppService $questionService;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getQuestionHomeList')]
    public function getQuestionHomeList(QuestionAppRequest $request): ResponseInterface
    {
        return $this->success($this->questionService->getQuestionHomeList($request->all()));
    }

    /**
     * 做题记录.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getUserQuestion'), Auth('app')]
    public function getUserQuestion(QuestionAppRequest $request): ResponseInterface
    {
        return $this->success($this->questionService->getUserQuestion($request->all()));
    }

    /**
     * 交换题目收藏状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('changeErrorCollect'), Auth('app')]
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
     * 获取做题排行榜.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getRankingCustomDate')]
    public function getRankingCustomDate(): ResponseInterface
    {
        return $this->success($this->historyService->getRankingCustomDate($this->request->all()));
    }

    /**
     * 获取我的做题排名.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getRankingMe'), Auth('app')]
    public function getRankingMe(): ResponseInterface
    {
        return $this->success($this->historyService->getRankingMe(user('app')->getId()));
    }

    /**
     * 获取我的做题排名.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getRankingMeCustomDate'), Auth('app')]
    public function getRankingMeCustomDate(): ResponseInterface
    {
        return $this->success($this->historyService->getRankingMeCustomDate(user('app')->getId(), $this->request->all()));
    }

    /**
     * 获取题目综合报告图表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getReport'), Auth('app')]
    public function getReport(): ResponseInterface
    {
        return $this->success($this->historyService->getReport(user('app')->getId()));
    }

    /**
     * 获取课程对应题目.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getCourseQuestion'), Auth('app')]
    public function getCourseQuestion(QuestionAppRequest $request): ResponseInterface
    {
        return $this->success($this->questionService->getAppCourseQuestion($request->all()));
    }

    /**
     * 获取单个题目.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('read/{id}'), Auth('app')]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->questionService->readQuestion($id)['data'] ?? []);
    }
}
