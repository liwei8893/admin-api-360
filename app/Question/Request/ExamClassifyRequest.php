<?php

declare(strict_types=1);

namespace App\Question\Request;

use Mine\MineFormRequest;

/**
 * 试卷分类验证数据类.
 */
class ExamClassifyRequest extends MineFormRequest
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
            // 菜单名称 验证
            'name' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            // 菜单名称 验证
            'name' => 'required',
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'id' => '主键',
            'parent_id' => '父ID',
            'level' => '组级集合',
            'name' => '菜单名称',
        ];
    }
}
