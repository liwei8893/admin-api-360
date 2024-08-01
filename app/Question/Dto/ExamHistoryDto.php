<?php

namespace App\Question\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 试卷记录Dto （导入导出）
 */
#[ExcelData]
class ExamHistoryDto implements MineModelExcel
{
    #[ExcelProperty(value: "主键", index: 0)]
    public string $id;

    #[ExcelProperty(value: "用户ID", index: 1)]
    public string $user_id;

    #[ExcelProperty(value: "试题ID", index: 2)]
    public string $exam_id;

    #[ExcelProperty(value: "用户输入的答案", index: 3)]
    public string $user_answer;

    #[ExcelProperty(value: "0错误；1正确", index: 4)]
    public string $is_right;

    #[ExcelProperty(value: "收藏错题本1收藏,0不收藏", index: 5)]
    public string $is_collect;

    #[ExcelProperty(value: "创建时间", index: 6)]
    public string $created_at;

    #[ExcelProperty(value: "更新时间", index: 7)]
    public string $updated_at;

    #[ExcelProperty(value: "删除时间", index: 8)]
    public string $deleted_at;


}
