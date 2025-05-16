<?php
declare(strict_types=1);


namespace App\Play\Request;

use Mine\MineFormRequest;

/**
 * 成语接龙验证数据类
 */
class PlayIdiomRequest extends MineFormRequest
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
            //棋盘 验证
            'board' => 'required',
            //提示词 验证
            'words' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            //棋盘 验证
            'board' => 'required',
            //提示词 验证
            'words' => 'required',
        ];
    }


    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => '关卡等级',
            'board' => '棋盘',
            'words' => '提示词',
        ];
    }

}
