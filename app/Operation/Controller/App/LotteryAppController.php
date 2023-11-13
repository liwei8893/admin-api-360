<?php

declare(strict_types=1);

namespace App\Operation\Controller\App;

use App\Operation\Request\LotteryUserRequest;
use App\Operation\Service\LotteryPrizeService;
use App\Operation\Service\LotteryUserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 抽奖名单控制器
 * Class LotteryUserController.
 */
#[Controller(prefix: 'operation/app/lottery')]
class LotteryAppController extends MineController
{
    /**
     * 业务处理服务
     * LotteryUserService.
     */
    #[Inject]
    protected LotteryUserService $userService;

    #[Inject]
    protected LotteryPrizeService $prizeService;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getList')]
    public function getList(LotteryUserRequest $request): ResponseInterface
    {
        return $this->success($this->prizeService->getList($request->all()));
    }

    /**
     * 检测活动是否有抽奖资格
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('hasPermission/{id}'), Auth('app')]
    public function hasPermission(int $id): ResponseInterface
    {
        return $this->success(['hasPermission' => $this->userService->hasPermission($id)]);
    }

    /**
     * 新增.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('saveLotteryUser/{id}'), Auth('app')]
    public function saveLotteryUser(int $id): ResponseInterface
    {
        return $this->success($this->userService->saveLotteryUser($id));
    }
}
