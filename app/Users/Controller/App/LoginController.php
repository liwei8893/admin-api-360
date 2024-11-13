<?php

declare(strict_types=1);

namespace App\Users\Controller\App;

use App\Users\Request\UsersAppLoginRequest;
use App\Users\Service\UsersAppLoginService;
use EasyWeChat\Kernel\Exceptions\HttpException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

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
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('wxLogin')]
    public function wxLogin(UsersAppLoginRequest $request): ResponseInterface
    {
        $params = $request->validated();
        return $this->service->wxLogin($params);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('wxLoginBindPhone')]
    public function wxLoginBindPhone(UsersAppLoginRequest $request): ResponseInterface
    {
        $params = $request->validated();
        return $this->success($this->service->wxLoginBindPhone($params));
    }

    /**
     * 登出.
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('logout'), Auth('app')]
    public function logout(): ResponseInterface
    {
        $this->service->logout();
        return $this->success();
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('changePassword'), Auth('app')]
    public function changePassword(UsersAppLoginRequest $request): ResponseInterface
    {
        return $this->service->changePassword($request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 微信jsSdk初始化配置
     * @param UsersAppLoginRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     * @throws HttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[PostMapping('jsSdkAuth')]
    public function jsSdkAuth(UsersAppLoginRequest $request): ResponseInterface
    {
        return $this->success($this->service->jsSdkAuth($request->input('url')));
    }
}
