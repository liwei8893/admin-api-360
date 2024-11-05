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

namespace App\Ai\Request;
 
use Mine\MineFormRequest;

/**
 * 知识点分类验证数据类
 */
class AiKnowsClassifyRequest extends MineFormRequest
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
            //父ID 验证
            'parent_id' => 'required',
            //组级集合 验证
            'level' => 'required',
            //菜单名称 验证
            'name' => 'required',

        ];
    }

    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            //父ID 验证
            'parent_id' => 'required',
            //组级集合 验证
            'level' => 'required',
            //菜单名称 验证
            'name' => 'required',

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
            'parent_id' => '父ID',
            'level' => '组级集合',
            'name' => '菜单名称',

        ];
    }

}
