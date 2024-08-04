<?php
namespace App\Play\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 成语接龙Dto （导入导出）
 */
#[ExcelData]
class PlayIdiomDto implements MineModelExcel
{
    #[ExcelProperty(value: "主键", index: 0)]
    public string $id;

    #[ExcelProperty(value: "关卡等级", index: 1)]
    public string $level;

    #[ExcelProperty(value: "棋盘", index: 2)]
    public string $board;

    #[ExcelProperty(value: "提示词", index: 3)]
    public string $words;

    #[ExcelProperty(value: "创建时间", index: 4)]
    public string $created_at;

    #[ExcelProperty(value: "更新时间", index: 5)]
    public string $updated_at;

    #[ExcelProperty(value: "删除时间", index: 6)]
    public string $deleted_at;


}