<?php

declare(strict_types=1);

namespace App\System\Controller;

use App\System\Request\SystemUserRequest;
use App\System\Service\SystemUserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Helper\LoginUser;
use Mine\Interfaces\UserServiceInterface;
use Mine\MineController;
use Mine\Vo\UserServiceVo;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class LoginController.
 */
#[Controller(prefix: 'system')]
class LoginController extends MineController
{
    #[Inject]
    protected SystemUserService $systemUserService;

    #[Inject]
    protected UserServiceInterface $userService;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    #[PostMapping('login')]
    public function login(SystemUserRequest $request): ResponseInterface
    {
        $requestData = $request->validated();
        $vo = new UserServiceVo();
        $vo->setUsername($requestData['username']);
        $vo->setPassword($requestData['password']);
        return $this->success(['token' => $this->userService->login($vo)]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    #[PostMapping('logout'), Auth]
    public function logout(): ResponseInterface
    {
        $this->userService->logout();
        return $this->success();
    }

    /**
     * 用户信息.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getInfo'), Auth]
    public function getInfo(): ResponseInterface
    {
        return $this->success($this->systemUserService->getInfo());
    }

    /**
     * 刷新token.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    #[PostMapping('refresh')]
    public function refresh(LoginUser $user): ResponseInterface
    {
        return $this->success(['token' => $user->refresh()]);
    }
}
