<?php

declare(strict_types=1);

namespace App\Users\Request;

use Hyperf\Validation\Rule;
use Mine\MineFormRequest;

/**
 * 用户表验证数据类.
 */
class UsersScoreRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    /**
     * 更换手机号.
     * @return string[]
     */
    public function changeRules(): array
    {
        return [
            'type' => ['required', Rule::in(['0', '1'])],
            'score' => 'required|integer',
            'user_id' => 'required|integer',
            'channel' => 'string',
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
