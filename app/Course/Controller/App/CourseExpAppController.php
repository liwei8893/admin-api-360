<?php

declare(strict_types=1);

namespace App\Course\Controller\App;

use App\Course\Service\CourseIndexService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'course/app/exp')]
class CourseExpAppController extends MineController
{
    #[Inject]
    protected CourseIndexService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('read/{id}')]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getPageList')]
    public function getPageList(): ResponseInterface
    {
        $params = $this->request->all();
        $params['orderBy'] = ['sort'];
        $params['orderType'] = ['asc'];
        return $this->success($this->service->getPageList($params, false));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getList')]
    public function getList(): ResponseInterface
    {
        $params = $this->request->all();
        $params['orderBy'] = ['sort'];
        $params['orderType'] = ['asc'];
        return $this->success($this->service->getList($params, false));
    }
}
