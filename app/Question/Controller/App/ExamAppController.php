<?php

declare(strict_types=1);

namespace App\Question\Controller\App;

use App\Question\Request\ExamAppRequest;
use App\Question\Service\ExamClassifyService;
use App\Question\Service\ExamHistoryService;
use App\Question\Service\ExamService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use JsonException;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'question/app/exam')]
class ExamAppController extends MineController
{
    #[Inject]
    protected ExamService $service;

    #[Inject]
    protected ExamClassifyService $classifyService;

    #[Inject]
    protected ExamHistoryService $historyService;

    /**
     * 获取分类列表
     * @param ExamAppRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getClassify')]
    public function getClassify(ExamAppRequest $request): ResponseInterface
    {
        $params = $request->all();
        $params['status'] = 1;
        $params['orderBy'] = ['sort', 'id'];
        $params['orderType'] = ['desc', 'asc'];
        return $this->success($this->classifyService->getTreeList($params));
    }

    /**
     * 获取题目列表
     * @param ExamAppRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getExamList'), Auth('app')]
    public function getExamList(ExamAppRequest $request): ResponseInterface
    {
        $params = $request->all();
        return $this->success($this->service->getAppExamList($params));
    }

    /**
     * 交换题目收藏状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('changeErrorCollect'), Auth('app')]
    public function changeErrorCollect(ExamAppRequest $request): ResponseInterface
    {
        return $this->historyService->changeErrorCollect($request->all()) ? $this->success() : $this->error();
    }

    /**
     * 提交做题记录
     * @param ExamAppRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    #[PostMapping('saveExamHistory'), Auth('app')]
    public function saveExamHistory(ExamAppRequest $request): ResponseInterface
    {
        return $this->success($this->historyService->appSave($request->all()));
    }

    /**
     * 自动组卷
     * @param ExamAppRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getExamAuto'), Auth('app')]
    public function getExamAuto(ExamAppRequest $request): ResponseInterface
    {
        return $this->success($this->service->getExamAuto($request->all()));
    }

    /**
     * 获取做题记录列表
     * @param ExamAppRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getExamHistoryList'), Auth('app')]
    public function getExamHistoryList(ExamAppRequest $request): ResponseInterface
    {
        return $this->success($this->service->getExamHistoryList($request->all()));
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

}
