<?php

declare(strict_types=1);

namespace App\Course\Request;

use Mine\MineFormRequest;

/**
 * 讲一讲审核验证数据类.
 */
class TalkRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    public function talkSaveRules(): array
    {
        return [
            'url' => 'required',
            'course_period_id' => 'required',
        ];
    }

    public function talkDeleteRules(): array
    {
        return ['id' => 'required'];
    }

    public function talkVoteRules(): array
    {
        return ['id' => 'required'];
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
