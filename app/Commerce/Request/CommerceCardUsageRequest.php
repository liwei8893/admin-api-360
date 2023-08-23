<?php

declare(strict_types=1);

namespace App\Commerce\Request;

use Mine\MineFormRequest;

/**
 * 电商卡使用记录验证数据类.
 */
class CommerceCardUsageRequest extends MineFormRequest
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
            'id' => 'ID',
            'card_id' => '卡号',
            'user_id' => '用户ID',
        ];
    }
}
