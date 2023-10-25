<?php

declare(strict_types=1);

namespace App\Operation\Request;

use Mine\MineFormRequest;

/**
 * 抽奖管理验证数据类.
 */
class LotteryRequest extends MineFormRequest
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
            // 抽奖活动名称 验证
            'name' => 'required',
            // 抽奖活动开始时间 验证
            'start_time' => 'required',
            // 抽奖活动结束时间 验证
            'end_time' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            // 抽奖活动名称 验证
            'name' => 'required',
            // 抽奖活动开始时间 验证
            'start_time' => 'required',
            // 抽奖活动结束时间 验证
            'end_time' => 'required',
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
            'name' => '抽奖活动名称',
            'start_time' => '抽奖活动开始时间',
            'end_time' => '抽奖活动结束时间',
        ];
    }
}
