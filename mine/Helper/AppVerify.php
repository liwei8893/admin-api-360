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

use Psr\SimpleCache\InvalidArgumentException;
use Xmo\JWTAuth\JWT;

class AppVerify
{
    protected JWT $jwt;

    /**
     * AppVerify constructor.
     * @param string $scene 场景，默认为default
     */
    public function __construct(string $scene = 'api')
    {
        /* @var JWT $this->jwt */
        $this->jwt = \Hyperf\Support\make(JWT::class)->setScene($scene);
    }

    /**
     * 验证token.
     * @throws InvalidArgumentException
     */
    public function check(?string $token = null, string $scene = 'api'): bool
    {
        try {
            if ($this->jwt->checkToken($token, $scene, true, true, true)) {
                return true;
            }
        } catch (\Throwable $e) {
            return false;
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

    /**
     * 获取当前API的信息.
     */
    public function getUserInfo(): array
    {
        return $this->jwt->getParserData();
    }

    /**
     * 获取当前ID.
     */
    public function getId(): string
    {
        return (string) $this->jwt->getParserData()['id'];
    }

    /**
     * 获取当前APP_ID.
     */
    public function getAppId(): string
    {
        return (string) $this->jwt->getParserData()['app_id'];
    }

    /**
     * 获取Token.
     * @throws InvalidArgumentException
     */
    public function getToken(array $apiInfo): string
    {
        return $this->jwt->getToken($apiInfo);
    }

    /**
     * 刷新token.
     * @throws InvalidArgumentException
     */
    public function refresh(): string
    {
        return $this->jwt->refreshToken();
    }
}
