<?php

declare(strict_types=1);

namespace App\System\Request;

use Mine\MineFormRequest;

class SmsRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    /**
     * 发私信验证
     */
    public function getForgotPwdSmsRules(): array
    {
        return [
            'mobile' => 'required',
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'mobile' => '手机号',
            'code' => '短信验证码',
        ];
    }
}
