<?php

declare(strict_types=1);

namespace App\Question\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 试卷表Dto （导入导出）.
 */
#[ExcelData]
class ExamDto implements MineModelExcel
{
    #[ExcelProperty(value: '主键', index: 0)]
    public string $id;

    #[ExcelProperty(value: '分类ID', index: 1)]
    public string $classify_id;

    #[ExcelProperty(value: '年级ID', index: 2)]
    public string $grade_id;

    #[ExcelProperty(value: '科目ID', index: 3)]
    public string $subject_id;

    #[ExcelProperty(value: '试题类型:1:单选题 2:多选题 4:判断题 5:问答题 6:填空题', index: 4)]
    public string $ques_type;

    #[ExcelProperty(value: '试题题目', index: 5)]
    public string $ques_title;

    #[ExcelProperty(value: '试题题干', index: 6)]
    public string $ques_stem;

    #[ExcelProperty(value: '文本题干', index: 7)]
    public string $ques_stem_text;

    #[ExcelProperty(value: '选项/问题参考答案/填空题：参考答案', index: 8)]
    public string $ques_option;

    #[ExcelProperty(value: '正确答案/填空题：答案验证规则1完全一致2仅顺序一致3仅供参考4未设置', index: 9)]
    public string $right_answer;

    #[ExcelProperty(value: '试题解析', index: 10)]
    public string $ques_analysis;

    #[ExcelProperty(value: '试题难度:1:易 2:中 3:难', index: 11)]
    public string $ques_difficulty;

    #[ExcelProperty(value: '状态 (1正常 0停用)', index: 12)]
    public string $status;

    #[ExcelProperty(value: '排序', index: 13)]
    public string $sort;

    #[ExcelProperty(value: '创建人', index: 14)]
    public string $created_by;

    #[ExcelProperty(value: '修改人', index: 15)]
    public string $updated_by;

    #[ExcelProperty(value: '创建时间', index: 16)]
    public string $created_at;

    #[ExcelProperty(value: '更新时间', index: 17)]
    public string $updated_at;

    #[ExcelProperty(value: '删除时间', index: 18)]
    public string $deleted_at;
}
