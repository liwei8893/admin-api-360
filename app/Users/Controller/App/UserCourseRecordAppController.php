<?php

declare(strict_types=1);

namespace App\Users\Controller\App;

use App\Users\Request\UserCourseRecordRequest;
use App\Users\Service\UserCourseRecordService;
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
    #[GetMapping('lastRecord'), Auth('app')]
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
     * 获取听课排行榜,上周.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getRankingCustomDate')]
    public function getRankingCustomDate(): ResponseInterface
    {
        return $this->success($this->service->getRankingCustomDate($this->request->all()));
    }

    /**
     * 排行榜我的排名.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getRankingMe'), Auth('app')]
    public function getRankingMe(): ResponseInterface
    {
        return $this->success($this->service->getRankingMe(user('app')->getId()));
    }

    /**
     * 排行榜我的排名.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getRankingMeCustomDate'), Auth('app')]
    public function getRankingMeCustomDate(): ResponseInterface
    {
        return $this->success($this->service->getRankingMeCustomDate(user('app')->getId(), $this->request->all()));
    }

    /**
     * 综合报告.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getReport'), Auth('app')]
    public function getReport(): ResponseInterface
    {
        return $this->success($this->service->getReport(user('app')->getId()));
    }

    /**
     * 听课记录.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getUserRecord'), Auth('app')]
    public function getUserRecord(): ResponseInterface
    {
        return $this->success($this->service->getUserRecord());
    }

    /**
     * 听课记录分页.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getUserRecordPageList'), Auth('app')]
    public function getUserRecordPageList(): ResponseInterface
    {
        return $this->success($this->service->getUserRecordPageList($this->request->all()));
    }

    /**
     * 记录课程观看时间.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('setWatchTime'), Auth('app')]
    public function setWatchTime(UserCourseRecordRequest $request): ResponseInterface
    {
        return $this->service->setWatchTime($request->all()) ? $this->success() : $this->error();
    }
}
