<?php
namespace App\Ai\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 评测报告Dto （导入导出）
 */
#[ExcelData]
class AiAssessReportDto implements MineModelExcel
{
    #[ExcelProperty(value: "ID", index: 0)]
    public string $id;

    #[ExcelProperty(value: "用户ID", index: 1)]
    public string $user_id;

    #[ExcelProperty(value: "知识点ID", index: 2)]
    public string $knows_id;

    #[ExcelProperty(value: "难度,1-3", index: 3)]
    public string $difficulty;

    #[ExcelProperty(value: "是否完成评测:1完成,0未完成", index: 4)]
    public string $is_assess_done;

    #[ExcelProperty(value: "知识点总数", index: 5)]
    public string $knows_count;

    #[ExcelProperty(value: "已掌握知识点数量", index: 6)]
    public string $knows_mastered_count;

    #[ExcelProperty(value: "未掌握知识点数量", index: 7)]
    public string $knows_unmastered_count;

    #[ExcelProperty(value: "知识点掌握率", index: 8)]
    public string $knows_mastered_rate;

    #[ExcelProperty(value: "题目总数", index: 9)]
    public string $ques_count;

    #[ExcelProperty(value: "正确题目数", index: 10)]
    public string $ques_correct_count;

    #[ExcelProperty(value: "错误题目数", index: 11)]
    public string $ques_incorrect_count;

    #[ExcelProperty(value: "题目正确率", index: 12)]
    public string $ques_correct_rate;

    #[ExcelProperty(value: "创建时间", index: 13)]
    public string $created_at;

    #[ExcelProperty(value: "更新时间", index: 14)]
    public string $updated_at;

    #[ExcelProperty(value: "删除时间", index: 15)]
    public string $deleted_at;


}