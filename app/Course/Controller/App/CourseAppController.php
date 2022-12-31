<?php

declare(strict_types=1);

namespace App\Course\Controller\App;

use App\Course\Request\CourseAppRequest;
use App\Course\Service\CoursePeriodService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'course/app')]
class CourseAppController extends MineController
{
    #[Inject]
    protected CoursePeriodService $periodService;

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
}
