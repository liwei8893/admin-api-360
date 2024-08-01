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

namespace App\Question\Request;

use Mine\MineFormRequest;

/**
 * 试卷记录验证数据类
 */
class ExamHistoryRequest extends MineFormRequest
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
            //试题ID 验证
            'exam_id' => 'required',

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
            //试题ID 验证
            'exam_id' => 'required',

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
            'exam_id' => '试题ID',

        ];
    }

}
