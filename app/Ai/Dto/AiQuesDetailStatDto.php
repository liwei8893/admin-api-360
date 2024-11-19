<?php
namespace App\Ai\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 题目详情统计Dto （导入导出）
 */
#[ExcelData]
class AiQuesDetailStatDto implements MineModelExcel
{
    #[ExcelProperty(value: "ID", index: 0)]
    public string $id;

    #[ExcelProperty(value: "题目ID", index: 1)]
    public string $ques_id;

    #[ExcelProperty(value: "总做题人数", index: 2)]
    public string $total_user_count;

    #[ExcelProperty(value: "正确题目数", index: 3)]
    public string $ques_correct_count;

    #[ExcelProperty(value: "错误题目数", index: 4)]
    public string $ques_incorrect_count;

    #[ExcelProperty(value: "题目正确率", index: 5)]
    public string $ques_correct_rate;

    #[ExcelProperty(value: "创建时间", index: 6)]
    public string $created_at;

    #[ExcelProperty(value: "更新时间", index: 7)]
    public string $updated_at;

    #[ExcelProperty(value: "删除时间", index: 8)]
    public string $deleted_at;


}