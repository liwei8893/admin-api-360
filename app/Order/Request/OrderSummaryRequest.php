<?php

declare(strict_types=1);

namespace App\Order\Request;

use Mine\MineFormRequest;

/**
 * 核单记录验证数据类.
 */
class OrderSummaryRequest extends MineFormRequest
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
            // 用户等级 验证
            'level' => 'required',
            // 是否添加微信 验证
            'has_wechat' => 'required',
            // 是否接通电话 验证
            'has_connect' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            // 用户等级 验证
            'level' => 'required',
            // 是否添加微信 验证
            'has_wechat' => 'required',
            // 是否接通电话 验证
            'has_connect' => 'required',
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
            'user_id' => '用户id',
            'order_id' => '订单ID',
            'level' => '用户等级',
            'has_wechat' => '是否添加微信',
            'has_connect' => '是否接通电话',
        ];
    }
}
