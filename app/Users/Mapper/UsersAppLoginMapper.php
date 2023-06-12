<?php

declare(strict_types=1);

namespace App\Users\Mapper;

use App\Users\Model\User;
use App\Users\Model\UsersLog;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractMapper;
use Mine\MineRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UsersAppLoginMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = User::class;
    }

    /**
     * 用手机号检测用户是否存在.
     */
    public function checkUserByMobile(mixed $mobile, array $select = ['*']): Model|Builder|null
    {
        return User::query()->where('mobile', $mobile)->select($select)->first();
    }

    public function getUserInfoByOpenId(string $openId, array $select = ['*']): Model|Builder|null
    {
        if (empty($openId)) {
            return null;
        }
        return $this->model::query()->where('wx_openid', $openId)->select($select)->first();
    }

    /**
     * 检查用户密码
     */
    public function checkPass(string $password, string $hash): bool
    {
        return User::passwordVerify($password, $hash);
    }

    /**
     * 是否初始密码
     */
    public function hasSimplePwd(array $userModel): bool
    {
        $simplePwd = substr((string) $userModel['mobile'], -6);
        if ($this->checkPass($simplePwd, $userModel['user_pass'])) {
            return true;
        }
        return false;
    }

    /**
     * 写登录日志.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setLoginLog(array $params): void
    {
        $request = container()->get(MineRequest::class);
        UsersLog::insert([
            'users_id' => $params['users_id'],
            'last_login_ip' => $request->ip(),
            'last_login_time' => time(),
            'continuous_count' => 1,
            'device_type' => $this->device($request->userAgent()),
        ]);
    }

    public function device($agent): int
    {
        $keywords = ['nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile', 'MicroMessenger'];
        if (preg_match('/(' . implode('|', $keywords) . ')/i', strtolower($agent))) {
            return 1;
        }
        return 2;
    }
}
