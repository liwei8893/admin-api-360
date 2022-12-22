<?php

declare(strict_types=1);

namespace App\System\Request;

use Mine\MineFormRequest;

/**
 * 区域字典验证数据类.
 */
class AreaRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    public function getAreaRules(): array
    {
        return [
            'parent_id' => 'required',
        ];
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
            'parent_id' => '地区代码',
        ];
    }
}
