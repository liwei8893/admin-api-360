<?php

declare(strict_types=1);

namespace App\Operation\Controller\Banner;

use App\Operation\Request\BannerRequest;
use App\Operation\Service\BannerService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'operation/app/banner')]
class BannerAppController extends MineController
{
    #[Inject]
    protected BannerService $service;

    /**
     * 获取轮播图.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getBanner')]
    public function getBanner(BannerRequest $request): ResponseInterface
    {
        $params = $request->all();
        $params['select'] = $params['select'] ?? 'id,banner_img';
        $params['status'] = 0;
        $params['states'] = 0;
        return $this->success($this->service->getList($params, false));
    }
}
