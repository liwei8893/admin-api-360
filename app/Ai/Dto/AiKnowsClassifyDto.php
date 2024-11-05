<?php

namespace App\Ai\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 知识点分类Dto （导入导出）
 */
#[ExcelData]
class AiKnowsClassifyDto implements MineModelExcel
{
    #[ExcelProperty(value: "主键", index: 0)]
    public string $id;

    #[ExcelProperty(value: "父ID", index: 1)]
    public string $parent_id;

    #[ExcelProperty(value: "组级集合", index: 2)]
    public string $level;

    #[ExcelProperty(value: "菜单名称", index: 3)]
    public string $name;

    #[ExcelProperty(value: "年级", index: 4)]
    public string $grade;

    #[ExcelProperty(value: "科目", index: 5)]
    public string $subject;

    #[ExcelProperty(value: "考试占比", index: 6)]
    public string $ratio;

    #[ExcelProperty(value: "状态 (1正常 0停用)", index: 7)]
    public string $status;

    #[ExcelProperty(value: "排序", index: 8)]
    public string $sort;

    #[ExcelProperty(value: "创建时间", index: 9)]
    public string $created_at;

    #[ExcelProperty(value: "更新时间", index: 10)]
    public string $updated_at;

    #[ExcelProperty(value: "删除时间", index: 11)]
    public string $deleted_at;


}
