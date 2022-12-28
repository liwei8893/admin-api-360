<?php

declare(strict_types=1);

namespace App\Users\Controller\App;

use App\Users\Service\UserCourseRecordService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 听课记录控制器
 * Class UserCourseRecordController.
 */
#[Controller(prefix: 'users/app/courseRecord')]
class UserCourseRecordAppController extends MineController
{
    /**
     * 业务处理服务
     * UserCourseRecordService.
     */
    #[Inject]
    protected UserCourseRecordService $service;

    /**
     * 获取最后一次观看课程记录.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('lastRecord'),Auth('app')]
    public function lastRecord(): ResponseInterface
    {
        return $this->success($this->service->lastRecord());
    }

    /**
     * 获取听课排行榜.
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
