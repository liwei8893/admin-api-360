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
 * 题目详情统计验证数据类
 */
class AiQuesDetailStatRequest extends MineFormRequest
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
            //总做题人数 验证
            'total_user_count' => 'required',
            //正确题目数 验证
            'ques_correct_count' => 'required',
            //错误题目数 验证
            'ques_incorrect_count' => 'required',
            //题目正确率 验证
            'ques_correct_rate' => 'required',

        ];
    }
    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            //总做题人数 验证
            'total_user_count' => 'required',
            //正确题目数 验证
            'ques_correct_count' => 'required',
            //错误题目数 验证
            'ques_incorrect_count' => 'required',
            //题目正确率 验证
            'ques_correct_rate' => 'required',

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
            'ques_id' => '题目ID',
            'total_user_count' => '总做题人数',
            'ques_correct_count' => '正确题目数',
            'ques_incorrect_count' => '错误题目数',
            'ques_correct_rate' => '题目正确率',

        ];
    }

}