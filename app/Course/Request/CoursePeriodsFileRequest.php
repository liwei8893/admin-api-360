<?php
declare(strict_types=1);

namespace App\Course\Request;

use Mine\MineFormRequest;

/**
 * 章节文件验证数据类
 */
class CoursePeriodsFileRequest extends MineFormRequest
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
            //章节ID 验证
            'periods_id' => 'required',
            //文件ID 验证
            'file_id' => 'required',
            //文件名称 验证
            'file_name' => 'required',
            //排序 验证
            'sort' => 'required',

        ];
    }
    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            //文件ID 验证
            'file_id' => 'required',
            //文件名称 验证
            'file_name' => 'required',
            //排序 验证
            'sort' => 'required',

        ];
    }

    
    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => 'ID',
            'periods_id' => '章节ID',
            'file_id' => '文件ID',
            'file_name' => '文件名称',
            'sort' => '排序',

        ];
    }

}
