<?php

declare(strict_types=1);

namespace App\Users\Request;

use Mine\MineFormRequest;

/**
 * 用户表验证数据类.
 */
class UsersAppLoginRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    public function loginRules(): array
    {
        return [
            'mobile' => 'required|regex:/^1\d{10}$/',
            'user_pass' => 'required_without:sms_code|string|between:6,20',
            'sms_code' => 'required_without:user_pass|integer|digits:6',
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
        ];
    }
}
