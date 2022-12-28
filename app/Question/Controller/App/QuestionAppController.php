<?php

declare(strict_types=1);

namespace App\Question\Controller\App;

use App\Question\Service\QuestionHistoryService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
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
    protected QuestionHistoryService $service;


    /**
     * 获取做题排行榜.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getRanking')]
    public function getRanking(): ResponseInterface
    {
        return $this->success($this->service->getRanking());
    }

    /**
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getRankingMe'),Auth('app')]
    public function getRankingMe(): ResponseInterface
    {
        return $this->success($this->service->getRankingMe());
    }

    /**
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getReport'),Auth('app')]
    public function getReport(): ResponseInterface
    {
        return $this->success($this->service->getReport());
    }
}
