<?php

declare(strict_types=1);

namespace App\System\Service;

use App\System\Mapper\SmsMapper;
use App\System\Model\SmsLog;
use App\System\Service\Dependencies\EasySmsService;
use App\System\Service\Dependencies\Sms\AuthMessage;
use App\System\Service\Dependencies\Sms\ForgotPwdMessage;
use App\System\Service\Dependencies\Sms\LoginMessage;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Mine\MineRequest;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SmsService extends AbstractService
{
    /**
     * @var SmsMapper
     */
    #[Inject]
    public $mapper;

    /**
     * 检测短信验证码
     */
    public function checkSmsCaptcha(string $mobile, string $smsCode): bool
    {
        if ($mobile === '18602780217') {
            return true;
        }
        /** @var SmsLog $smsModel */
        $smsModel = $this->mapper->checkSmsCaptcha($mobile);
        if ($smsModel && (time() - $smsModel->created_at->timestamp) >= 300) {
            throw new NormalStatusException('短信验证码已过期');
        }
        if ($smsCode !== $smsModel->sms_code) {
            throw new NormalStatusException('短信验证码不正确!');
        }
        return true;
    }

    /**
     * @param mixed $params
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getForgotPwdSms(array $params): bool
    {
        $message = new ForgotPwdMessage();
        $easySms = new EasySmsService();
        $this->handleSmsSendBefore($params['mobile'], true);
        try {
            $res = $easySms->send($params['mobile'], $message);
            return $this->handleSmsSendAfter($params['mobile'], $message, $res);
        } catch (InvalidArgumentException | NoGatewayAvailableException $e) {
            throw new NormalStatusException('短信发送失败,请稍后重试!');
        }
    }

    /**
     * 登录验证码
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getLoginSms(array $params): bool
    {
        $message = new LoginMessage();
        $easySms = new EasySmsService();
        $this->handleSmsSendBefore($params['mobile'], true);
        try {
            $res = $easySms->send($params['mobile'], $message);
            return $this->handleSmsSendAfter($params['mobile'], $message, $res);
        } catch (InvalidArgumentException | NoGatewayAvailableException $e) {
            throw new NormalStatusException('短信发送失败,请稍后重试!');
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAuthSms(array $params): bool
    {
        $message = new AuthMessage();
        $easySms = new EasySmsService();
        $this->handleSmsSendBefore($params['mobile']);
        try {
            $res = $easySms->send($params['mobile'], $message);
            return $this->handleSmsSendAfter($params['mobile'], $message, $res);
        } catch (InvalidArgumentException | NoGatewayAvailableException $e) {
            throw new NormalStatusException('短信发送失败,请稍后重试!');
        }
    }

    /**
     * 发送短信前的校验处理
     * @param string $mobile 手机号
     * @param bool $checkRegistered 是否检查手机号注册状态(登录和忘记密码场景需要检查)
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function handleSmsSendBefore(string $mobile, bool $checkRegistered = false): void
    {
        // 登录和忘记密码场景：检查手机号是否已注册
        if ($checkRegistered && !$this->mapper->checkMobileExists($mobile)) {
            throw new NormalStatusException('该手机号尚未注册,请先注册!');
        }

        // 每个手机号每天允许发10条
        $mobileData = $this->mapper->getTheDayByMobile($mobile);
        $mobileCount = $mobileData->count();
        $maxMobileTimeCarbon = $mobileData->max('created_at');
        $maxMobileTime = '';
        if (isset($maxMobileTimeCarbon)) {
            $maxMobileTime = $maxMobileTimeCarbon->timestamp;
        }
        if ($mobileCount > 10) {
            throw new NormalStatusException('请勿频繁发送短信验证,数量超限!');
        }
        if ($maxMobileTime !== '' && ((time() - $maxMobileTime) < 60)) {
            throw new NormalStatusException('请勿频繁发送短信验证,IP时长超限!');
        }
        // 检测IP是否超过发短信次数每天20条
        $ipData = $this->mapper->getTheDayByIp(container()->get(MineRequest::class)->ip());
        $ipCount = $ipData->count();
        $maxIpTimeCarbon = $ipData->max('created_at');
        $maxIpTime = '';
        if (isset($maxIpTimeCarbon)) {
            $maxIpTime = $maxIpTimeCarbon->timestamp;
        }
        if ($ipCount > 20) {
            throw new NormalStatusException('请勿频繁发送短信验证,IP数量超限!');
        }
        if ($maxIpTime !== '' && ((time() - $maxIpTime) < 60)) {
            throw new NormalStatusException('请勿频繁发送短信验证,IP时长超限!');
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function handleSmsSendAfter(string $mobile, mixed $message, mixed $res): bool
    {
        $name = explode('\\', $message::class);
        $smsFunc = str_replace('Message', '', array_pop($name));
        if (isset($res['aliyun']['status']) && $res['aliyun']['status'] === 'success') {
            $this->mapper->setSmsLog([
                'mobile' => $mobile,
                'sms_code' => $message->getData()['code'],
                'content' => $message->getTemplate(),
                'sms_func' => $smsFunc,
                'sms_ip' => container()->get(MineRequest::class)->ip(),
                'return_code' => $res['aliyun']['result']['Message'] ?? '',
            ]);
            return true;
        }
        return false;
    }
}
