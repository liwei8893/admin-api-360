<?php

declare(strict_types=1);

namespace App\Sta\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

#[ExcelData]
class HasCourseRecordDto implements MineModelExcel
{
    #[ExcelProperty(value: '学员姓名', index: 0)]
    public string $user_name;

    #[ExcelProperty(value: '学员年级', index: 1)]
    public string $grade_name;

    #[ExcelProperty(value: '手机号', index: 2)]
    public string $mobile;

    #[ExcelProperty(value: '平台编号', index: 3)]
    public string $platform;

    #[ExcelProperty(value: '是否听课', index: 4)]
    public string $has_record;

    #[ExcelProperty(value: '是否登录', index: 5)]
    public string $has_login;

    #[ExcelProperty(value: '听课总时长(分钟)', index: 6)]
    public string $watch_time_sum;

    #[ExcelProperty(value: '报名时间', index: 7)]
    public string $order_created_at;

    #[ExcelProperty(value: '最后登录时间', index: 8)]
    public string $last_login_time;
}
