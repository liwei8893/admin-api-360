<?php

declare(strict_types=1);

namespace App\Commerce\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 电商管理Dto （导入导出）.
 */
#[ExcelData]
class CommerceCardDto implements MineModelExcel
{
    #[ExcelProperty(value: '卡号', index: 0)]
    public string $card_id;

    #[ExcelProperty(value: '课程ID', index: 1)]
    public string $course_id;

    #[ExcelProperty(value: '课程名称', index: 2, customField: 'course.title')]
    public string $course_name;

    #[ExcelProperty(value: '是否使用', index: 3)]
    public string $status;

    #[ExcelProperty(value: '创建时间', index: 4)]
    public string $created_at;

    #[ExcelProperty(value: '更新时间', index: 5)]
    public string $updated_at;

    #[ExcelProperty(value: '激活链接', index: 6)]
    public string $link;
}
