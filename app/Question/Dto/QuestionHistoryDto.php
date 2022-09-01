<?php

namespace App\Question\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 错题表Dto （导入导出）
 */
#[ExcelData]
class QuestionHistoryDto implements MineModelExcel
{
    #[ExcelProperty(value: "课程名称", index: 0, customField: 'question.knows.name')]
    public string $questionKnowsName;

    #[ExcelProperty(value: "题目标题", index: 1, customField: 'question.ques_title')]
    public string $questionQues_title;

    #[ExcelProperty(value: "题干", index: 2, customField: 'question.ques_stem_text')]
    public string $questionQues_stem_text;

    #[ExcelProperty(value: "科目", index: 3, customField: 'question.question_subject.label')]
    public string $questionQuestion_subjectLabel;

    #[ExcelProperty(value: "类型", index: 4, customField: 'question.question_type.label')]
    public string $questionQuestion_typeLabel;

    #[ExcelProperty(value: "正确答案", index: 5, customField: 'question.right_answer')]
    public string $questionRight_answer;

    #[ExcelProperty(value: "答案", index: 6)]
    public string $user_answer;

    #[ExcelProperty(value: "是否正确(1正确,0错误)", index: 7)]
    public string $is_right;

    #[ExcelProperty(value: "错题本(1收藏,0未收藏)", index: 8)]
    public string $is_collect;

    #[ExcelProperty(value: "做题时间", index: 9)]
    public string $created_at;

}