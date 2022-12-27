<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\System\Service\SmsService;
use App\Users\Mapper\UsersAppLoginMapper;
use App\Users\Model\Users;
use Exception;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Mine\Helper\MineCode;
use Mine\MineModel;
use Mine\MineRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

class UsersAppLoginService extends AbstractService
{
    /**
     * @var UsersAppLoginMapper
     */
    #[Inject]
    public $mapper;

    #[Inject]
    public SmsService $smsService;

    #[Inject]
    public UsersService $usersService;

    #[Inject]
    protected UserSalePlatformService $userSalePlatformService;

    /**
     * @param mixed $params
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     */
    public function login(array $params): array
    {
        try {
            $userinfo = $this->mapper->checkUserByMobile($params['mobile'], Users::COMMON_FIELDS);
            // 判断账号是否禁用
            if ($userinfo && (int) $userinfo['status'] !== MineModel::ENABLE) {
                throw new NormalStatusException('账号已被禁用,请联系课程顾问!');
            }
            // 判断登录方式 验证码
            if (! empty($params['sms_code'])) {
                $resSmsCode = $params['sms_code'];
                $this->smsService->checkSmsCaptcha($params['mobile'], $resSmsCode);
                // 验证码通过 判断是否有用户,没有就注册为新用户
                if (! $userinfo && $userModel = $this->register($params)) {
                    return $this->loginAfter($userModel);
                }
                // 验证成功
                return $this->loginAfter($userinfo);
            }
            // 密码验证
            if ($this->mapper->checkPass($params['user_pass'], $userinfo['user_pass'])) {
                // 验证成功
                return $this->loginAfter($userinfo);
            }
            // 密码验证不成功
            throw new NormalStatusException('账号或密码不正确!');
        } catch (Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                throw new NormalStatusException('用户不存在!', MineCode::NO_DATA);
            }
            throw new NormalStatusException($e->getMessage());
        }
    }

    /**
     * @param mixed $userModel
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     */
    public function loginAfter(Users $userModel): array
    {
        $console = console();
        $console->info('开始loginAfter');
        $request = container()->get(MineRequest::class);
        // 生成jwt token
        $console->info('获取token');
        $token = user('app')->getToken(['id' => $userModel->id]);
        $console->info($token);
        // 更新最后登录时间
        $userModel->update([
            'last_login_ip' => $request->ip(),
            'last_login_time' => time(),
            'remember_token' => $token,
        ]);
        // 插入登录日志表
        $this->mapper->setLoginLog(['users_id' => $userModel->id]);
        // 已购买的课程id列表挂载到用户信息中
        $userModel->load(['orders' => static function ($query) {
            $query->normalOrder()->isNotExpire()->select(['user_id', 'shop_id']);
        }]);
        // 挂载会员类型,到期时间
        $userModel->load(['vipType']);
        // 复制用户模型
        $result = $userModel->toArray();
        // 添加是否初始密码
        $result['isSimplePwd'] = $this->mapper->hasSimplePwd($userModel);
        // 添加token
        $result['remember_token'] = $token;
        return $result;
    }

    /**
     * 前台注册.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function register(array $data): int
    {
        if ($this->usersService->existsByMobile($data['mobile'])) {
            throw new NormalStatusException('手机号已存在');
        }
        // 获取平台编号,挂载到数组
        $data = $this->userSalePlatformService->withPlatformNum($data);
        // 合并初始化参数
        $data = array_merge([
            'mobile' => $data['mobile'],
            'user_name' => $data['user_name'] ?? $this->usersService->getInitUserName($data['mobile']),
            'user_nickname' => $data['user_name'] ?? $this->usersService->getInitUserName($data['mobile']),
            'real_name' => $data['real_name'] ?? $this->usersService->getInitUserName($data['mobile']),
            'user_pass' => $data['user_pass'] ?? $this->usersService->getInitPassword($data['mobile']),
            'avatar' => config('hxt-app.defaultAvatar'),
            'user_type' => 1,
            'status' => 1,
            'sex' => 3,
            'last_login_ip' => container()->get(MineRequest::class)->ip(),
            'last_login_time' => time(),
        ], $data);
        return $this->mapper->save($data);
    }

    /**
     * 登出.
     * @throws InvalidArgumentException
     */
    public function logout(): void
    {
        $user = user('app');
        $user->getJwt()->logout();
    }

    /**
     * 重置密码
     * @param mixed $params
     */
    public function resetPassword($params): bool
    {
        // 查找用户信息
        $userinfo = $this->mapper->checkUserByMobile($params['mobile'], Users::COMMON_FIELDS);
        // 判断账号是否禁用
        if ($userinfo && (int) $userinfo['status'] !== MineModel::ENABLE) {
            throw new NormalStatusException('账号已被禁用,请联系课程顾问!');
        }
        // 验证短信
        $resSmsCode = $params['sms_code'];
        $this->smsService->checkSmsCaptcha($params['mobile'], $resSmsCode);
        // 修改密码
        return $this->usersService->initUserPassword($userinfo['id'], $params['user_pass']);
    }

    /**
     * 修改密码
     * @param mixed $params
     */
    public function changePassword($params): bool
    {
        // 修改密码
        return $this->usersService->initUserPassword(user()->getId(), $params['user_pass']);
    }
}
