<?php

declare(strict_types=1);

namespace Mine\Interfaces;

use Mine\Vo\UserServiceVo;

/**
 * 用户服务抽象
 */
interface UserServiceInterface
{
    public function login(UserServiceVo $userServiceVo);

    public function logout();
}
