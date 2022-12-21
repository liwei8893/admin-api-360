<?php

declare(strict_types=1);

namespace App\Users\Controller\App;

use App\Users\Request\UsersAppLoginRequest;
use App\Users\Service\UsersAppLoginService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\InvalidArgumentException;

#[Controller(prefix: 'users/app')]
class LoginController extends MineController
{
    #[Inject]
    protected UsersAppLoginService $service;

    /**
     * 登录.
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('login')]
    public function login(UsersAppLoginRequest $request): ResponseInterface
    {
        $params = $request->validated();
        return $this->success($this->service->login($params));
    }

    /**
     * 登出.
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('logout'),Auth('app')]
    public function logout(): ResponseInterface
    {
        $this->service->logout();
        return $this->success();
    }

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
     * 重置密码
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('resetPassword')]
    public function resetPassword(UsersAppLoginRequest $request): ResponseInterface
    {
        return $this->service->resetPassword($request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 修改密码
     * @param UsersAppLoginRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('changePassword'),Auth('app')]
    public function changePassword(UsersAppLoginRequest $request): ResponseInterface
    {
        return $this->service->changePassword($request->validated()) ? $this->success() : $this->error();
    }
}
