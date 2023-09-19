<?php

declare(strict_types=1);

namespace App\Commerce\Request;

use Mine\MineFormRequest;

/**
 * 电商管理验证数据类.
 */
class CommerceCardRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    public function activateCardRules(): array
    {
        return [
            // 课程ID 验证
            'mobile' => 'required|regex:/^1\d{10}$/',
            'sms_code' => 'required|integer|digits:6',
            'card_id' => 'required|integer|digits:8',
        ];
    }

    /**
     * 新增数据验证规则
     * return array.
     */
    public function saveRules(): array
    {
        return [
            // 课程ID 验证
            'course_id' => 'required',
        ];
    }

    public function generateCardRules(): array
    {
        return [
            // 课程ID 验证
            'course_id' => 'required',
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
            // 课程ID 验证
            'course_id' => 'required',
            // 是否使用 验证
            'status' => 'required',
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'card_id' => '卡号',
            'course_id' => '课程ID',
            'status' => '是否使用',
            'num' => '数量',
        ];
    }
}
