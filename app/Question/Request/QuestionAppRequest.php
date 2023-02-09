<?php

declare(strict_types=1);

namespace App\Question\Request;

use Mine\MineFormRequest;

/**
 * 题库管理验证数据类.
 */
class QuestionAppRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    public function readQuestionRules(): array
    {
        return [
            'id' => 'required',
        ];
    }

    public function getQuestionHomeListRules(): array
    {
        return [
            'type' => 'required',
            'subject' => 'required',
        ];
    }

    public function changeErrorCollectRules(): array
    {
        return ['id' => 'required'];
    }

    public function getCourseQuestionRules(): array
    {
        return [
            'period_id' => 'required',
            // 1练一练,2测一测
            'channel' => 'required',
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
        ];
    }
}
