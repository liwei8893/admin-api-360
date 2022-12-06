<?php

declare(strict_types=1);

namespace App\System\Service;

use App\System\Mapper\SmsMapper;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;

class SmsService extends AbstractService
{
    /**
     * @var SmsMapper
     */
    public $mapper;

    /**
     * 检测短信验证码
     * @param $mobile
     * @param $smsCode
     */
    public function checkSmsCaptcha($mobile, $smsCode): bool
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
}
