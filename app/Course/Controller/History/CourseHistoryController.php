<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Course\Controller\History;

use App\Course\Service\CourseHistoryService;
use App\Order\Request\OrderHistoryRequest;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
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
}
