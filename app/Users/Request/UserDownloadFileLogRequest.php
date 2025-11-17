<?php
declare(strict_types=1);

namespace App\Users\Request;

use Mine\MineFormRequest;

/**
 * 用户下载文件记录验证数据类
 */
class UserDownloadFileLogRequest extends MineFormRequest
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

        ];
    }

    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [

        ];
    }


    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => '',
            'user_id' => '用户ID',
            'periods_id' => '章节ID',
            'file_id' => '文件ID',
            'file_name' => '文件名称',
            'periods_name' => '章节名称',
            'course_name' => '课程名称',

        ];
    }

}
