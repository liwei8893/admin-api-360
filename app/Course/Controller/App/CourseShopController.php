<?php

declare(strict_types=1);

namespace App\Course\Controller\App;

use App\Course\Request\CourseShopRequest;
use App\Course\Service\CourseShopService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'course/app/shop')]
class CourseShopController extends MineController
{
    #[Inject]
    protected CourseShopService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getList')]
    public function getList(): ResponseInterface
    {
        $params = ['withCourse' => true, 'orderBy' => ['sort'], 'orderType' => ['desc']];
        return $this->success($this->service->getAppList($params));
    }

    /**
     * @param CourseShopRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getFirst')]
    public function getFirst(CourseShopRequest $request): ResponseInterface
    {
        return $this->success($this->service->getFirst((int) $request->input('id')));
    }
}
