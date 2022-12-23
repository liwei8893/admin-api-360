<?php

declare(strict_types=1);

namespace App\Score\Controller;

use App\Score\Request\ScoreShopRequest;
use App\Score\Service\ScoreShopService;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'score/app/shop')]
class ScoreShopAppController extends MineController
{
    #[Inject]
    protected ScoreShopService $service;

    /**
     * 获取积分商店头像列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getAvatarPageList')]
    public function getAvatarPageList(ScoreShopRequest $request): ResponseInterface
    {
        return $this->success($this->service->getAvatarPageList($request->all()));
    }

    /**
     * 积分兑换课程头像.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[PostMapping('exchange'),Auth('app')]
    public function exchange(ScoreShopRequest $request): ResponseInterface
    {
        return $this->success($this->service->exchange($request->validated()));
    }
}
