<?php

declare(strict_types=1);

namespace App\Users\Request;

use Mine\MineFormRequest;

/**
 * 用户备注验证数据类.
 */
class UserRemarkRequest extends MineFormRequest
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
            // 备注类型,1常规,2售后 验证
            'type' => 'required',
            // 备注 验证
            'remark' => 'required',
            'user_id' => 'required',
            'after_sale_type' => 'required_if:type,2',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            // 备注类型,1常规,2售后 验证
            'type' => 'required',
            // 备注 验证
            'remark' => 'required',
            'user_id' => 'required',
            'after_sale_type' => 'required_if:type,2',
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'id' => '',
            'user_id' => '关联用户ID',
            'type' => '备注类型,常规,售后',
            'remark' => '备注',
            'created_id' => '创建人ID',
        ];
    }
}
