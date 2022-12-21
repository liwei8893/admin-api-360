<?php

declare(strict_types=1);

namespace App\System\Service;

use App\System\Mapper\SmsMapper;
use App\System\Service\Dependencies\EasySmsService;
use App\System\Service\Dependencies\Sms\ForgotPwdMessage;
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
     * @param mixed $mobile
     * @param mixed $smsCode
     */
    public function checkSmsCaptcha(mixed $mobile, mixed $smsCode): bool
    {
        $smsModel = $this->mapper->checkSmsCaptcha($mobile);
        if ($smsModel && isset($smsModel['created_at']) && (time() - strtotime($smsModel['created_at'])) >= 180) {
            throw new NormalStatusException('短信验证码已过期');
        }
        if ($smsCode !== $smsModel['sms_code']) {
            throw new NormalStatusException('短信验证码不正确!');
        }
        return true;
    }

    /**
     * @param mixed $params
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NoGatewayAvailableException
     * @throws NotFoundExceptionInterface
     */
    public function getForgotPwdSms(array $params): bool
    {
        $message = new ForgotPwdMessage();
        $easySms = new EasySmsService();
        $this->handleSmsSendBefore($params['mobile']);
        $res = $easySms->send($params['mobile'], $message);
        return $this->handleSmsSendAfter($params['mobile'], $message, $res);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function handleSmsSendBefore(string $mobile): void
    {
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
        $smsFunc = array_pop($name);
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
