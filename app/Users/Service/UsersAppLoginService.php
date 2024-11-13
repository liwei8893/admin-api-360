<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\System\Service\SmsService;
use App\Users\Mapper\UsersAppLoginMapper;
use App\Users\Model\User;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\OfficialAccount\Application;
use Exception;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Mine\Helper\MineCode;
use Mine\MineModel;
use Mine\MineRequest;
use Mine\MineResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use function Hyperf\Config\config;

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

    #[Inject]
    protected MineResponse $response;

    /**
     * @param mixed $params
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     */
    public function login(array $params): array
    {
        try {
            // 是否调试账号
            $isDebug = $params['mobile'] === 18602780217;

            $userinfo = $this->mapper->checkUserByMobile($params['mobile'], User::COMMON_FIELDS);
            // 判断账号是否禁用
            if ($userinfo && (int)$userinfo['status'] !== MineModel::ENABLE) {
                throw new NormalStatusException('账号已被禁用,请联系课程顾问!');
            }
            // 判断登录方式 验证码
            if (!empty($params['sms_code'])) {
                $resSmsCode = $params['sms_code'];
                $this->smsService->checkSmsCaptcha((string)$params['mobile'], (string)$resSmsCode);
                // 验证码通过 判断是否有用户,没有就注册为新用户
                if (!$userinfo && $userModel = $this->register($params)) {
                    return $this->loginAfter($userModel);
                }
                // 验证成功
                return $this->loginAfter($userinfo);
            }
            if (!$userinfo) {
                throw new NormalStatusException('该账号未注册,请联系课程顾问!');
            }
            // 密码验证
            if ($isDebug) {
                console()->info(date('Y-m-d H:i:s') . '-开始验证密码');
            }
            if ($this->mapper->checkPass($params['user_pass'], $userinfo['user_pass'])) {
                if ($isDebug) {
                    console()->info(date('Y-m-d H:i:s') . '-密码验证完成');
                }
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
     * 前台注册.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function register(array $data): MineModel|Model
    {
        if ($this->usersService->existsByMobile((string)$data['mobile'])) {
            throw new NormalStatusException('手机号已存在');
        }
        // 获取平台编号,挂载到数组
        $data = $this->userSalePlatformService->withPlatformNum($data);
        // 合并初始化参数
        $data = array_merge([
            'mobile' => $data['mobile'],
            'user_name' => $data['user_name'] ?? $this->usersService->getInitUserName((string)$data['mobile']),
            'user_nickname' => $data['user_name'] ?? $this->usersService->getInitUserName((string)$data['mobile']),
            'real_name' => $data['real_name'] ?? $this->usersService->getInitUserName((string)$data['mobile']),
            'user_pass' => $data['user_pass'] ?? $this->usersService->getInitPassword((string)$data['mobile']),
            'avatar' => config('hxt-app.defaultAvatar'),
            'user_type' => 1,
            'status' => 1,
            'sex' => 3,
            'last_login_ip' => container()->get(MineRequest::class)->ip(),
            'last_login_time' => time(),
        ], $data);
        return $this->mapper->create($data);
    }

    /**
     * @param mixed $userModel
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     */
    public function loginAfter(User $userModel): array
    {
        // 是否调试账号
        $isDebug = $userModel->mobile === '18602780217';

        $request = container()->get(MineRequest::class);
        // 生成jwt token
        $token = user('app')->getToken(['id' => $userModel->id]);
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
        $orderIds = $userModel->orders->pluck('shop_id');
        // 挂载会员类型,到期时间
        $userModel->load(['vipType']);
        // 复制用户模型
        $result = $userModel->toArray();
        $result['orders'] = $orderIds->toArray();
        // 添加是否初始密码
        if ($isDebug) {
            console()->info(date('Y-m-d H:i:s') . '-开始验证密码');
        }
        $result['isSimplePwd'] = $this->mapper->hasSimplePwd($userModel);
        if ($isDebug) {
            console()->info(date('Y-m-d H:i:s') . '-密码验证结束');
        }
        // 添加token
        $result['remember_token'] = $token;
        return $result;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     */
    public function wxLogin(array $params): ResponseInterface
    {
        // 有mobile绑定手机号
        $code = $params['code'];
        try {
            $config = config('wechat.official_account.default');
            $app = new Application($config);
            $oauth = $app->getOauth();
            $user = $oauth->scopes(['snsapi_base'])->userFromCode($code);
        } catch (\EasyWeChat\Kernel\Exceptions\InvalidArgumentException $e) {
            throw new NormalStatusException('openId获取失败，请刷新页面重试!');
        }
        $openId = $user->getId();
        if (!$openId) {
            throw new NormalStatusException('openId获取失败，请刷新页面重试!');
        }
        $userinfo = $this->mapper->getUserInfoByOpenId($openId, User::COMMON_FIELDS);
        // 判断账号是否禁用
        if ($userinfo && (int)$userinfo['status'] !== MineModel::ENABLE) {
            throw new NormalStatusException('账号已被禁用,请联系课程顾问!');
        }
        // 未绑定手机号
        if (!$userinfo) {
            return $this->response->error('请绑定手机号!', 210, ['openId' => $openId]);
        }
        return $this->response->success(null, $this->loginAfter($userinfo));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     */
    public function wxLoginBindPhone(array $params): array
    {
        try {
            $mobile = $params['mobile'];
            $smsCode = $params['sms_code'];
            $openId = $params['openId'];
            $this->smsService->checkSmsCaptcha((string)$mobile, (string)$smsCode);
            $userinfo = $this->mapper->checkUserByMobile($mobile, User::COMMON_FIELDS);
            // 判断账号是否禁用
            if ($userinfo && (int)$userinfo['status'] !== MineModel::ENABLE) {
                throw new NormalStatusException('账号已被禁用,请联系课程顾问!');
            }
            // 验证码通过 判断是否有用户,没有就注册为新用户
            if (!$userinfo) {
                /** @var User $userinfo */
                $userinfo = $this->register($params);
            }
            // 验证成功
            $userinfo->wxgzh_openid = $openId;
            $userinfo->wx_openid = $openId;
            $userinfo->save();
            return $this->loginAfter($userinfo);
        } catch (Exception $e) {
            throw new NormalStatusException($e->getMessage());
        }
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
     */
    public function resetPassword(array $params): bool
    {
        // 查找用户信息
        $userinfo = $this->mapper->checkUserByMobile($params['mobile'], User::COMMON_FIELDS);
        // 判断账号是否禁用
        if (!$userinfo) {
            throw new NormalStatusException('该账号未注册,请联系课程顾问!');
        }
        if ((int)$userinfo['status'] !== MineModel::ENABLE) {
            throw new NormalStatusException('账号已被禁用,请联系课程顾问!');
        }
        // 验证短信
        $resSmsCode = $params['sms_code'];
        $this->smsService->checkSmsCaptcha((string)$params['mobile'], (string)$resSmsCode);
        // 修改密码
        return $this->usersService->initUserPassword($userinfo['id'], $params['user_pass']);
    }

    /**
     * 修改密码
     */
    public function changePassword(array $params): bool
    {
        // 修改密码
        return $this->usersService->initUserPassword(user('app')->getId(), $params['user_pass']);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws HttpException
     * @throws ServerExceptionInterface
     */
    public function jsSdkAuth($url): array
    {
        $config = config('wechat.official_account.default');
        $app = new Application($config);
        return $app->getUtils()->buildJsSdkConfig(
            url: $url,
            jsApiList: ['updateAppMessageShareData', 'updateTimelineShareData', 'chooseWXPay',],
            openTagList: [],
            debug: false,
        );
    }
}
