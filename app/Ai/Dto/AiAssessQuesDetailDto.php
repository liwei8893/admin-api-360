<?php
namespace App\Ai\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 评测题目明细Dto （导入导出）
 */
#[ExcelData]
class AiAssessQuesDetailDto implements MineModelExcel
{
    #[ExcelProperty(value: "ID", index: 0)]
    public string $id;

    #[ExcelProperty(value: "评测报告表ID", index: 1)]
    public string $assess_report_id;

    #[ExcelProperty(value: "题目ID", index: 2)]
    public string $ques_id;

    #[ExcelProperty(value: "1级知识点ID", index: 3)]
    public string $knows_level1_id;

    #[ExcelProperty(value: "1级知识点名称", index: 4)]
    public string $knows_level1_name;

    #[ExcelProperty(value: "2级知识点ID", index: 5)]
    public string $knows_level2_id;

    #[ExcelProperty(value: "2级知识点名称", index: 6)]
    public string $knows_level2_name;

    #[ExcelProperty(value: "知识点难度", index: 7)]
    public string $knows_difficulty;

    #[ExcelProperty(value: "建议答题时间", index: 8)]
    public string $rec_answer_duration;

    #[ExcelProperty(value: "用户答案", index: 9)]
    public string $user_answer;

    #[ExcelProperty(value: "是否正确", index: 10)]
    public string $is_right;

    #[ExcelProperty(value: "答题时间", index: 11)]
    public string $user_answer_duration;

    #[ExcelProperty(value: "创建时间", index: 12)]
    public string $created_at;

    #[ExcelProperty(value: "更新时间", index: 13)]
    public string $updated_at;

    #[ExcelProperty(value: "删除时间", index: 14)]
    public string $deleted_at;


}