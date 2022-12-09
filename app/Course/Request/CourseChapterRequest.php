<?php

declare(strict_types=1);

namespace App\Course\Request;

use Mine\MineFormRequest;

/**
 * 课程大纲验证数据类.
 */
class CourseChapterRequest extends MineFormRequest
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
            'parent_id' => 'required',
            'course_basis_id' => 'required',
            'title' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            'parent_id' => 'required',
            'course_basis_id' => 'required',
            'course_period' => 'required|array',
            'title' => 'required',
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
