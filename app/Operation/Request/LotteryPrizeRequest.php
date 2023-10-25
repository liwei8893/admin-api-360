<?php

declare(strict_types=1);

namespace App\Operation\Request;

use Mine\MineFormRequest;

/**
 * 抽奖奖品验证数据类.
 */
class LotteryPrizeRequest extends MineFormRequest
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
            // 奖品数量 验证
            'num' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            // 奖品数量 验证
            'num' => 'required',
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'num' => '奖品数量',
        ];
    }
}
