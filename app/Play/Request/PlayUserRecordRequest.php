<?php
declare(strict_types=1);

namespace App\Play\Request;

use Mine\MineFormRequest;

/**
 * 用户游戏记录验证数据类
 */
class PlayUserRecordRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [];
    }


    /**
     * 新增数据验证规则
     * return array
     */
    public function saveRules(): array
    {
        return [
            //成语接龙关卡等级 验证
            'idiom_level' => 'required',
            //数独最高分数 验证
            'sudoku_score' => 'required',

        ];
    }

    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            //成语接龙关卡等级 验证
            'idiom_level' => 'required',
            //数独最高分数 验证
            'sudoku_score' => 'required',

        ];
    }


    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => '主键',
            'user_id' => '用户ID',
            'idiom_level' => '成语接龙关卡等级',
            'sudoku_score' => '数独最高分数',

        ];
    }

}
