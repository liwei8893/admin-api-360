<?php

declare(strict_types=1);

namespace App\Users\Request;

use Mine\MineFormRequest;

/**
 * 用户表验证数据类.
 */
class UsersAppRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    public function updateInfoRules(): array
    {
        return [
            'mobile' => 'required|regex:/^1\d{10}$/',
            'user_name' => 'required',
        ];
    }

    public function setAvatarRules(): array
    {
        return [
            'id' => 'required',
            'type' => 'required',
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
            'user_pass' => '密码',
            'sms_code' => '短信验证码',
        ];
    }
}
