<?php

declare(strict_types=1);

namespace App\Course\Controller\App;

use App\Course\Request\CourseAppRequest;
use App\Course\Service\CourseBasisService;
use App\Course\Service\CourseChapterService;
use App\Course\Service\CoursePeriodService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;

#[Controller(prefix: 'course/app')]
class CourseAppController extends MineController
{
    #[Inject]
    protected CoursePeriodService $periodService;

    #[Inject]
    protected CourseChapterService $chapterService;

    #[Inject]
    protected CourseBasisService $basisService;

    /**
     * 获取课程链接.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getUrl/{id}')]
    public function getUrl(int $id): ResponseInterface
    {
        return $this->success($this->periodService->getUrl($id));
    }

    /**
     * 获取课程列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getCourseList')]
    public function getCourseList(CourseAppRequest $request): ResponseInterface
    {
        return $this->success($this->basisService->getAppPageList($request->all(), false));
    }

    /**
     * 学习中心根据年级月份提供学习计划.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    #[GetMapping('getPlanMonth')]
    public function getPlanMonth(CourseAppRequest $request): ResponseInterface
    {
        return $this->success($this->periodService->getPlanMonth($request->all()));
    }

    /**
     * 获取课程信息.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getCourseInfo/{id}')]
    public function getCourseInfo(int $id): ResponseInterface
    {
        $model = $this->basisService->read($id);
        if (!$model) {
            return $this->error('课程不存在!');
        }
        return $this->success($model);
    }

    /**
     * 获取课程章节信息.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getChapter/{id}')]
    public function getChapter(int $id): ResponseInterface
    {
        return $this->success($this->chapterService->getChapter($id));
    }

    /**
     * 获取课程章节信息.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getPeriod/{id}')]
    public function getPeriod(int $id): ResponseInterface
    {
        return $this->success($this->periodService->getPeriod($id));
    }

    /**
     * 标签查询课程.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getSearch')]
    public function getSearch(CourseAppRequest $request): ResponseInterface
    {
        $ids = $request->input('ids');
        return $this->success($this->periodService->getSearch($ids));
    }
}
