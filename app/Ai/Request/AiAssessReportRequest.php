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
 * 评测报告验证数据类
 */
class AiAssessReportRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [];
    }

    public function genRules(): array
    {
        return [
            'difficulty' => 'required',
            'knows_id' => 'required',
        ];
    }

    /**
     * 新增数据验证规则
     * return array
     */
    public function saveRules(): array
    {
        return [
            //知识点ID 验证
            'knows_id' => 'required',
            //难度,1-3 验证
            'difficulty' => 'required',
            //是否完成评测:1完成,0未完成 验证
            'is_assess_done' => 'required',
            //知识点总数 验证
            'knows_count' => 'required',
            //已掌握知识点数量 验证
            'knows_mastered_count' => 'required',
            //未掌握知识点数量 验证
            'knows_unmastered_count' => 'required',
            //知识点掌握率 验证
            'knows_mastered_rate' => 'required',
            //题目总数 验证
            'ques_count' => 'required',
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
            //知识点ID 验证
            'knows_id' => 'required',
            //难度,1-3 验证
            'difficulty' => 'required',
            //是否完成评测:1完成,0未完成 验证
            'is_assess_done' => 'required',
            //知识点总数 验证
            'knows_count' => 'required',
            //已掌握知识点数量 验证
            'knows_mastered_count' => 'required',
            //未掌握知识点数量 验证
            'knows_unmastered_count' => 'required',
            //知识点掌握率 验证
            'knows_mastered_rate' => 'required',
            //题目总数 验证
            'ques_count' => 'required',
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
            'user_id' => '用户ID',
            'knows_id' => '知识点ID',
            'difficulty' => '难度,1-3',
            'is_assess_done' => '是否完成评测:1完成,0未完成',
            'knows_count' => '知识点总数',
            'knows_mastered_count' => '已掌握知识点数量',
            'knows_unmastered_count' => '未掌握知识点数量',
            'knows_mastered_rate' => '知识点掌握率',
            'ques_count' => '题目总数',
            'ques_correct_count' => '正确题目数',
            'ques_incorrect_count' => '错误题目数',
            'ques_correct_rate' => '题目正确率',

        ];
    }

}
