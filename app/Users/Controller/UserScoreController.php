<?php

declare(strict_types=1);

namespace App\Users\Controller;

use App\Users\Request\UsersScoreRequest;
use App\Users\Service\UserScoreService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'users/score'), Auth]
class UserScoreController extends MineController
{
    #[Inject]
    protected UserScoreService $service;

    /**
     * 后台管理员变更积分.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('change'), OperationLog('管理员变更积分')]
    public function change(UsersScoreRequest $request): ResponseInterface
    {
        $params = $request->validated();
        $params['channel_type'] = 0;
        $params['origin_id'] = 0;
        return $this->service->changeScore($params) ? $this->success() : $this->error();
    }
}
