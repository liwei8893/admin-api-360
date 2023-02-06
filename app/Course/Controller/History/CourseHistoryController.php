<?php

declare(strict_types=1);

namespace App\Course\Controller\History;

use App\Course\Service\CourseHistoryService;
use App\Order\Request\OrderHistoryRequest;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'course/history'), Auth]
class CourseHistoryController extends MineController
{
    #[Inject]
    protected CourseHistoryService $service;

    /**
     * 课程购买记录.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('course:history:index')]
    public function index(OrderHistoryRequest $request): ResponseInterface
    {
        return $this->success($this->service->getHistoryList($request->all()));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('batchChangeGrade'), Permission('course:history:batchChangeGrade,order:changeOrder'), OperationLog('批量修改购买年级')]
    public function batchChangeGrade(OrderHistoryRequest $request): ResponseInterface
    {
        return $this->service->batchChangeGrade($request->all()) ? $this->success() : $this->error();
    }
}
