<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\Course\Request;

use Mine\MineFormRequest;

/**
 * 课程报名配置表验证数据类
 */
class CourseSignupConfigRequest extends MineFormRequest
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
            'id' => '主键',
            'title' => '课程名称',
            'price' => '金额',
            'day' => '天数',
            'remark' => '备注',
            'created_by' => '创建者',
            'updated_by' => '更新者',
            'created_at' => '',
            'updated_at' => '',
            'deleted_at' => '',
        ];
    }

}