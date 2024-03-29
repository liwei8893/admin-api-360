<?php

declare(strict_types=1);

namespace App\Question\Request;

use Mine\MineFormRequest;

/**
 * 试卷表验证数据类.
 */
class ExamRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    public function changeSortRules(): array
    {
        return [
            'id' => 'required',
            'sort' => 'required',
        ];
    }

    /**
     * 新增数据验证规则
     * return array.
     */
    public function saveRules(): array
    {
        return [
            // 试题类型:1:单选题 2:多选题 4:判断题 5:问答题 6:填空题 验证
            'ques_type' => 'required',
            // 试题题干 验证
            'ques_stem' => 'required',
            // 正确答案/填空题：答案验证规则1完全一致2仅顺序一致3仅供参考4未设置 验证
            'right_answer' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            // 试题类型:1:单选题 2:多选题 4:判断题 5:问答题 6:填空题 验证
            'ques_type' => 'required',
            // 试题题干 验证
            'ques_stem' => 'required',
            // 正确答案/填空题：答案验证规则1完全一致2仅顺序一致3仅供参考4未设置 验证
            'right_answer' => 'required',
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'id' => '主键',
            'classify_id' => '分类ID',
            'ques_type' => '试题类型:1:单选题 2:多选题 4:判断题 5:问答题 6:填空题',
            'ques_stem' => '试题题干',
            'right_answer' => '正确答案/填空题：答案验证规则1完全一致2仅顺序一致3仅供参考4未设置',
        ];
    }
}
