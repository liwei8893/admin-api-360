<?php

declare(strict_types=1);

namespace App\Sta\Controller\App;

use App\Sta\Service\LearningReportService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'sta/app/learning/report')]
class LearningReportController extends MineController
{
    #[Inject]
    public LearningReportService $service;

    /**
     * 每周学习报告.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getReportByWeek'), Auth('app')]
    public function getReportByWeek(): ResponseInterface
    {
        return $this->success($this->service->getReportByWeek($this->request->all()));
    }
}
