<?php

declare(strict_types=1);

namespace App\Course\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

#[ExcelData]
class CourseBasisDto implements MineModelExcel
{
    #[ExcelProperty(value: '标题', index: 0)]
    public string $title;

    #[ExcelProperty(value: '课程价格', index: 1)]
    public string $price;

    #[ExcelProperty(value: '是否上架', index: 2)]
    public string $states;

    #[ExcelProperty(value: '是否报名', index: 3)]
    public string $is_signup;
}
