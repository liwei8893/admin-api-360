<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);

namespace Mine\Helper;

use App\System\Model\SystemRole;
use App\System\Model\SystemUser;
use App\System\Service\SystemUserService;
use Mine\Exception\TokenException;
use Mine\MineRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;
use Xmo\JWTAuth\JWT;

class LoginUser
{
    protected JWT $jwt;

    protected MineRequest $request;

    /**
     * LoginUser constructor.
     * @param string $scene 场景，默认为default
     */
    public function __construct(string $scene = 'default')
    {
        /* @var JWT $this- >jwt */
        $this->jwt = make(JWT::class)->setScene($scene);
    }

    /**
     * 验证token.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function check(?string $token = null, string $scene = 'default'): bool
    {
        try {
            if ($this->jwt->checkToken($token, $scene, true, true, true)) {
                return true;
            }
        } catch (InvalidArgumentException $e) {
            throw new TokenException(t('jwt.no_token'));
        } catch (Throwable $e) {
            throw new TokenException(t('jwt.no_login'));
        }

        return false;
    }

    /**
     * 获取JWT对象
     */
    public function getJwt(): Jwt
    {
        return $this->jwt;
    }

    public function getScene(): string
    {
        return $this->jwt->getParserData()['jwt_scene'];
    }

    /**
     * 获取当前登录用户信息.
     */
    public function getUserInfo(?string $token = null): array
    {
        return $this->jwt->getParserData($token);
    }

    /**
     * 获取当前登录用户名.
     */
    public function getUsername(): string
    {
        return $this->jwt->getParserData()['username'];
    }

    /**
     * 获取当前登录用户角色.
     */
    public function getUserRole(array $columns = ['id', 'name', 'code']): array
    {
        return SystemUser::find($this->getId(), ['id'])->roles()->get($columns)->toArray();
    }

    /**
     * 获取当前登录用户ID.
     */
    public function getId(): int
    {
        return $this->jwt->getParserData()['id'];
    }

    /**
     * 获取当前登录用户岗位.
     */
    public function getUserPost(array $columns = ['id', 'name', 'code']): array
    {
        return SystemUser::find($this->getId(), ['id'])->posts()->get($columns)->toArray();
    }

    /**
     * 获取当前token用户类型.
     */
    public function getUserType(): string
    {
        return $this->jwt->getParserData()['user_type'];
    }

    /**
     * 获取当前token用户部门ID.
     */
    public function getDeptId(): int
    {
        return (int) $this->jwt->getParserData()['dept_id'];
    }

    /**
     * 是否为超级管理员（创始人），用户禁用对创始人没用.
     */
    public function isSuperAdmin(): bool
    {
        return (int) env('SUPER_ADMIN') === $this->getId();
    }

    /**
     * 是否为管理员角色.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function isAdminRole(): bool
    {
        return in_array(SystemRole::find(env('ADMIN_ROLE'), ['code'])->code, container()->get(SystemUserService::class)->getInfo()['roles'], true);
    }

    /**
     * 获取Token.
     * @throws InvalidArgumentException
     */
    public function getToken(array $user): string
    {
        return $this->jwt->getToken($user);
    }

    /**
     * 刷新token.
     * @throws InvalidArgumentException
     */
    public function refresh(): string
    {
        return $this->jwt->refreshToken();
    }

    /**
     * 报名|修改有效期是否不需要审核.
     */
    public function isNoAuditRole(): bool
    {
        try {
            return in_array(SystemRole::find(6, ['code'])->code, container()->get(SystemUserService::class)->getInfo()['roles'], true);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return false;
        }
    }
}
