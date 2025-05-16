<?php
declare(strict_types=1);

namespace App\Crm\Request;

use Mine\MineFormRequest;

/**
 * 学习记录验证数据类
 */
class CrmStudyRecordRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [];
    }

    public function listRules(): array
    {
        return ['user_id' => 'required'];
    }


    /**
     * 新增数据验证规则
     * return array
     */
    public function saveRules(): array
    {
        return [
            //自增主键 验证
            'id' => 'required',

        ];
    }

    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            //自增主键 验证
            'id' => 'required',

        ];
    }


    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => '自增主键',

        ];
    }

}
