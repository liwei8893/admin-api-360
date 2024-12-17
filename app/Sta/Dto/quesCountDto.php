<?php

declare(strict_types=1);

namespace App\Sta\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

#[ExcelData]
class quesCountDto implements MineModelExcel
{
    #[ExcelProperty(value: '学员姓名', index: 0)]
    public string $user_name;

    #[ExcelProperty(value: '学员年级', index: 1)]
    public string $grade_name;

    #[ExcelProperty(value: '手机号', index: 2)]
    public string $mobile;

    #[ExcelProperty(value: '平台编号', index: 3)]
    public string $platform;

    #[ExcelProperty(value: '做题数量', index: 4)]
    public string $ques_count;

    #[ExcelProperty(value: '报名时间', index: 5)]
    public string $order_created_at;
}
