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
namespace App\Crm\Request;

use Mine\MineFormRequest;

/**
 * 用户时间线记录表验证数据类
 */
class CrmUserTimelineRequest extends MineFormRequest
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
            //用户ID 验证
            'user_id' => 'required',
            //创建人id 验证
            'created_by' => 'required',

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
            //创建人id 验证
            'created_by' => 'required',

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
            'user_id' => '用户ID',
            'created_by' => '创建人id',

        ];
    }

}