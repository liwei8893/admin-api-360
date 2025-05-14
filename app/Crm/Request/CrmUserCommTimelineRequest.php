<?php
declare(strict_types=1);

namespace App\Crm\Request;

use Mine\MineFormRequest;

/**
 * 用户沟通时间验证数据类
 */
class CrmUserCommTimelineRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [];
    }

    /**
     * 列表验证规则
     */
    public function listRules(): array
    {
        return [
            // 用户ID 验证
            'user_id' => 'required',
        ];
    }

    /**
     * 新增数据验证规则
     * return array
     */
    public function saveRules(): array
    {
        return [
            //用户ID 验证
            'user_id' => 'required',

        ];
    }

    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            //用户ID 验证
            'user_id' => 'required',

        ];
    }


    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => '主键ID',
            'user_id' => '用户ID',

        ];
    }

}
