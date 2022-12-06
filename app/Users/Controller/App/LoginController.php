<?php

declare(strict_types=1);

namespace App\Users\Controller\App;

use _PHPStan_76800bfb5\Nette\Neon\Exception;
use App\Users\Request\UsersAppLoginRequest;
use App\Users\Service\UsersAppLoginService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Exception\NormalStatusException;
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
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getUserInfo'),Auth('app')]
    public function getUserInfo(): ResponseInterface
    {
        return $this->success(user('app')->getUserInfo());
    }
}
