<?php

declare(strict_types=1);

namespace App\Sta\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

#[ExcelData]
class CourseHitsDto implements MineModelExcel
{
    #[ExcelProperty(value: '课程名称', index: 0, customField: 'courseBasis.title')]
    public string $title;

    #[ExcelProperty(value: '点击量', index: 1, customField: 'hits')]
    public string $hits;
}
