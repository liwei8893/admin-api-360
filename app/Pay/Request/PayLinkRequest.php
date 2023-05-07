<?php

declare(strict_types=1);

namespace App\Pay\Request;

use Mine\MineFormRequest;

/**
 * 付款链接验证数据类.
 */
class PayLinkRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    /**
     * 新增数据验证规则
     * return array.
     */
    public function saveRules(): array
    {
        return [
            // 备注 验证
            'remark' => 'required',
            // 平台编号 验证
            'platform' => 'required',
            // pay_config表ID 验证
            'config_id' => 'required',
            // pay_auth表ID 验证
            'auth_id' => 'required',
            'course_id' => 'required',
            'image' => 'required',
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
            'remark' => '备注',
            'platform_code' => '平台编号',
            'platform_name' => '平台名称',
            'config_id' => 'pay_config表ID',
            'auth_id' => 'pay_auth表ID',
        ];
    }
}
