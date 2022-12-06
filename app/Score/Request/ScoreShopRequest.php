<?php

declare(strict_types=1);

namespace App\Score\Request;

use Hyperf\Validation\Rule;
use Mine\MineFormRequest;

/**
 * 积分管理验证数据类.
 */
class ScoreShopRequest extends MineFormRequest
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
            'shop_type' => ['required', Rule::in(['avatar', 'courseBasis'])],
            'shop_id' => 'required|array',
            'score' => 'required|integer',
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
