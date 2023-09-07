<?php

declare(strict_types=1);

namespace App\Sta\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

#[ExcelData]
class PeriodHitsDto implements MineModelExcel
{
    #[ExcelProperty(value: '课程名称', index: 0, customField: 'courseBasis.title')]
    public string $course_basis_title;

    #[ExcelProperty(value: '章节名称', index: 1, customField: 'title')]
    public string $title;

    #[ExcelProperty(value: '点击量', index: 2, customField: 'real_hits')]
    public string $hits;
}
