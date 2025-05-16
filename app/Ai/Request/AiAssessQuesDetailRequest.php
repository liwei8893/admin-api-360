<?php
declare(strict_types=1);


namespace App\Ai\Request;

use Mine\MineFormRequest;

/**
 * 评测题目明细验证数据类
 */
class AiAssessQuesDetailRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [];
    }

    public function submitRules(): array
    {
        return [
            // 评测题目明细ID 验证
            'id' => 'required',
            //用户答案 验证
            'user_answer' => 'required',
            //答题时间 验证
            'user_answer_duration' => 'required',
        ];
    }

    /**
     * 新增数据验证规则
     * return array
     */
    public function saveRules(): array
    {
        return [
            //评测报告表ID 验证
            'assess_report_id' => 'required',
            //题目ID 验证
            'ques_id' => 'required',
            //1级知识点ID 验证
            'knows_level1_id' => 'required',
            //1级知识点名称 验证
            'knows_level1_name' => 'required',
            //2级知识点ID 验证
            'knows_level2_id' => 'required',
            //2级知识点名称 验证
            'knows_level2_name' => 'required',
            //知识点难度 验证
            'knows_difficulty' => 'required',
            //建议答题时间 验证
            'rec_answer_duration' => 'required',
            //用户答案 验证
            'user_answer' => 'required',
            //是否正确 验证
            'is_right' => 'required',
            //答题时间 验证
            'user_answer_duration' => 'required',

        ];
    }

    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            //评测报告表ID 验证
            'assess_report_id' => 'required',
            //题目ID 验证
            'ques_id' => 'required',
            //1级知识点ID 验证
            'knows_level1_id' => 'required',
            //1级知识点名称 验证
            'knows_level1_name' => 'required',
            //2级知识点ID 验证
            'knows_level2_id' => 'required',
            //2级知识点名称 验证
            'knows_level2_name' => 'required',
            //知识点难度 验证
            'knows_difficulty' => 'required',
            //建议答题时间 验证
            'rec_answer_duration' => 'required',
            //用户答案 验证
            'user_answer' => 'required',
            //是否正确 验证
            'is_right' => 'required',
            //答题时间 验证
            'user_answer_duration' => 'required',

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
            'assess_report_id' => '评测报告表ID',
            'ques_id' => '题目ID',
            'knows_level1_id' => '1级知识点ID',
            'knows_level1_name' => '1级知识点名称',
            'knows_level2_id' => '2级知识点ID',
            'knows_level2_name' => '2级知识点名称',
            'knows_difficulty' => '知识点难度',
            'rec_answer_duration' => '建议答题时间',
            'user_answer' => '用户答案',
            'is_right' => '是否正确',
            'user_answer_duration' => '答题时间',

        ];
    }

}
