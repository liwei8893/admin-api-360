<?php

declare(strict_types=1);

namespace App\Users\Controller\App;

use App\Users\Request\UsersAppRequest;
use App\Users\Service\UsersAppService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'users/app')]
class UserAppController extends MineController
{
    #[Inject]
    protected UsersAppService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getUserInfo'),Auth('app')]
    public function getUserInfo(): ResponseInterface
    {
        return $this->success(user('app')->getUserInfo());
    }

    /**
     * @param UsersAppRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('updateInfo'),Auth('app')]
    public function updateInfo(UsersAppRequest $request): ResponseInterface
    {
        $params = $request->all();
        return $this->success($this->service->updateInfo($params));
    }
}
