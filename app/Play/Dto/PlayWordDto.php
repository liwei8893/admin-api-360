<?php
namespace App\Play\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 单词游戏Dto （导入导出）
 */
#[ExcelData]
class PlayWordDto implements MineModelExcel
{
    #[ExcelProperty(value: "主键", index: 0)]
    public string $id;

    #[ExcelProperty(value: "单词", index: 1)]
    public string $word;

    #[ExcelProperty(value: "英式英标", index: 2)]
    public string $uk;

    #[ExcelProperty(value: "英式发音", index: 3)]
    public string $uk_speech;

    #[ExcelProperty(value: "美式英标", index: 4)]
    public string $us;

    #[ExcelProperty(value: "美式发音", index: 5)]
    public string $us_speech;

    #[ExcelProperty(value: "中文翻译", index: 6)]
    public string $trs;

    #[ExcelProperty(value: "卡片单词", index: 7)]
    public string $word_card;

    #[ExcelProperty(value: "创建时间", index: 8)]
    public string $created_at;

    #[ExcelProperty(value: "更新时间", index: 9)]
    public string $updated_at;

    #[ExcelProperty(value: "删除时间", index: 10)]
    public string $deleted_at;


}