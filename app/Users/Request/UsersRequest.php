<?php

declare(strict_types=1);

namespace App\Users\Request;

use Mine\MineFormRequest;

/**
 * 用户表验证数据类.
 */
class UsersRequest extends MineFormRequest
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
     *                  author:ZQ
     *                  time:2022-08-28 14:52
     */
    public function changeMobileRules(): array
    {
        return [
            'mobile' => 'required|regex:/^1[3456789]\d{9}$/',
            'userId' => 'required|integer',
        ];
    }

    /**
     * 批量更新平台.
     * @return string[]
     *                  author:ZQ
     *                  time:2022-08-28 11:36
     */
    public function batchChangePlatformRules(): array
    {
        return [
            'mobiles' => 'required',
            'platform' => 'required',
        ];
    }

    /**
     * 新增数据验证规则
     * return array.
     */
    public function saveRules(): array
    {
        return [
            'mobile' => 'required|regex:/^1[3456789]\d{9}$/',
            'grade_id' => 'required',
            'platform' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
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
