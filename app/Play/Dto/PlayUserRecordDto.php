<?php
namespace App\Play\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 用户游戏记录Dto （导入导出）
 */
#[ExcelData]
class PlayUserRecordDto implements MineModelExcel
{
    #[ExcelProperty(value: "主键", index: 0)]
    public string $id;

    #[ExcelProperty(value: "用户ID", index: 1)]
    public string $user_id;

    #[ExcelProperty(value: "成语接龙关卡等级", index: 2)]
    public string $idiom_level;

    #[ExcelProperty(value: "数独最高分数", index: 3)]
    public string $sudoku_score;

    #[ExcelProperty(value: "创建时间", index: 4)]
    public string $created_at;

    #[ExcelProperty(value: "更新时间", index: 5)]
    public string $updated_at;

    #[ExcelProperty(value: "删除时间", index: 6)]
    public string $deleted_at;


}