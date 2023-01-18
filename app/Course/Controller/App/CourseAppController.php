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
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

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
     * 获取试卷链接.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getUrl/{id}'), Auth('app')]
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
        if (! $model) {
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
}
