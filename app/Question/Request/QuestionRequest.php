<?php

declare(strict_types=1);

namespace App\Question\Request;

use Mine\MineFormRequest;

/**
 * 题库管理验证数据类.
 */
class QuestionRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    public function getCourseQuestionRules(): array
    {
        return [
            'course_basis_id' => 'required',
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
