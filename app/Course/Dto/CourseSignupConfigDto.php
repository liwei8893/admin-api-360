<?php
namespace App\Course\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 课程报名配置表Dto （导入导出）
 */
#[ExcelData]
class CourseSignupConfigDto implements MineModelExcel
{
    #[ExcelProperty(value: "主键", index: 0)]
    public string $id;

    #[ExcelProperty(value: "课程名称", index: 1)]
    public string $title;

    #[ExcelProperty(value: "金额", index: 2)]
    public string $price;

    #[ExcelProperty(value: "天数", index: 3)]
    public string $day;

    #[ExcelProperty(value: "备注", index: 4)]
    public string $remark;

    #[ExcelProperty(value: "创建者", index: 5)]
    public string $created_by;

    #[ExcelProperty(value: "更新者", index: 6)]
    public string $updated_by;

    #[ExcelProperty(value: "created_at", index: 7)]
    public string $created_at;

    #[ExcelProperty(value: "updated_at", index: 8)]
    public string $updated_at;

    #[ExcelProperty(value: "deleted_at", index: 9)]
    public string $deleted_at;


}