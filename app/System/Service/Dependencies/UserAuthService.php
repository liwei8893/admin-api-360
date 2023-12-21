<?php

declare(strict_types=1);

namespace App\System\Service\Dependencies;

use App\System\Mapper\SystemUserMapper;
use App\System\Model\SystemUser;
use Exception;
use Hyperf\Database\Model\ModelNotFoundException;
use Mine\Event\UserLoginAfter;
use Mine\Event\UserLoginBefore;
use Mine\Event\UserLogout;
use Mine\Exception\NormalStatusException;
use Mine\Exception\UserBanException;
use Mine\Helper\MineCode;
use Mine\Interfaces\UserServiceInterface;
use Mine\Vo\UserServiceVo;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * 用户登录.
 */
class UserAuthService implements UserServiceInterface
{
    /**
     * 登录.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function login(UserServiceVo $userServiceVo): string
    {
        $mapper = container()->get(SystemUserMapper::class);
        try {
            event(new UserLoginBefore(['username' => $userServiceVo->getUsername(), 'password' => $userServiceVo->getPassword()]));
            $userinfo = $mapper->checkUserByUsername($userServiceVo->getUsername())->toArray();
            $password = $userinfo['password'];
            unset($userinfo['password']);
            $userLoginAfter = new UserLoginAfter($userinfo);
            if ($mapper->checkPass($userServiceVo->getPassword(), $password)) {
                if (
                    ($userinfo['status'] == SystemUser::USER_NORMAL)
                    || ($userinfo['status'] == SystemUser::USER_BAN && $userinfo['id'] == \Hyperf\Support\env('SUPER_ADMIN'))
                ) {
                    $userLoginAfter->message = t('jwt.login_success');
                    $token = user()->getToken($userLoginAfter->userinfo);
                    $userLoginAfter->token = $token;
                    event($userLoginAfter);
                    return $token;
                }
                $userLoginAfter->loginStatus = false;
                $userLoginAfter->message = t('jwt.user_ban');
                event($userLoginAfter);
                throw new UserBanException();
            }
            $userLoginAfter->loginStatus = false;
            $userLoginAfter->message = t('jwt.password_error');
            event($userLoginAfter);
            throw new NormalStatusException();
        } catch (Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                throw new NormalStatusException(t('jwt.username_error'), MineCode::NO_DATA);
            }
            if ($e instanceof NormalStatusException) {
                throw new NormalStatusException(t('jwt.password_error'), MineCode::PASSWORD_ERROR);
            }
            if ($e instanceof UserBanException) {
                throw new NormalStatusException(t('jwt.user_ban'), MineCode::USER_BAN);
            }
            console()->error($e->getMessage());
            throw new NormalStatusException(t('jwt.unknown_error'));
        }
    }

    /**
     * 用户退出.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function logout()
    {
        $user = user();
        event(new UserLogout($user->getUserInfo()));
        $user->getJwt()->logout();
    }
}
