<?php

declare(strict_types=1);

namespace App\Course\Controller\App;

use App\Course\Service\SunService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'course/app/comment')]
class CommentSunController extends MineController
{
    #[Inject]
    protected SunService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getContentPageList')]
    public function getContentPageList(): ResponseInterface
    {
        return $this->success($this->service->getContentPageList($this->request->all()));
    }
}
