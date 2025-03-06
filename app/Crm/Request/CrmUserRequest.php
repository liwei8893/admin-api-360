<?php

declare(strict_types=1);

namespace App\Crm\Request;

use Mine\MineFormRequest;

/**
 * 用户表验证数据类.
 */
class CrmUserRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    public function saveDetailRules(): array
    {
        return ['user_id' => 'required|integer',];
    }

    /**
     * 批量更新平台.
     * @return string[]
     */
    public function batchDistroRules(): array
    {
        return [
            'userIds' => 'required|array',
            'adminId' => 'required|integer',
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
