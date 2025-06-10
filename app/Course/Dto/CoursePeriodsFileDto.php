<?php
namespace App\Course\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 章节文件Dto （导入导出）
 */
#[ExcelData]
class CoursePeriodsFileDto implements MineModelExcel
{
    #[ExcelProperty(value: "ID", index: 0)]
    public string $id;

    #[ExcelProperty(value: "章节ID", index: 1)]
    public string $periods_id;

    #[ExcelProperty(value: "文件ID", index: 2)]
    public string $file_id;

    #[ExcelProperty(value: "文件名称", index: 3)]
    public string $file_name;

    #[ExcelProperty(value: "排序", index: 4)]
    public string $sort;

    #[ExcelProperty(value: "创建时间", index: 5)]
    public string $created_at;

    #[ExcelProperty(value: "更新时间", index: 6)]
    public string $updated_at;


}