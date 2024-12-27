<?php

declare(strict_types=1);

namespace App\Sta\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

#[ExcelData]
class renewEarlyWarningDto implements MineModelExcel
{
    #[ExcelProperty(value: '学员姓名', index: 0)]
    public string $user_name;

    #[ExcelProperty(value: '手机号', index: 1)]
    public string $mobile;

    #[ExcelProperty(value: '平台编号', index: 2)]
    public string $platform;

    #[ExcelProperty(value: '听课节数', index: 3)]
    public string $course_record_count;

    #[ExcelProperty(value: '听课总时长秒', index: 4)]
    public string $course_record_sum_time;

    #[ExcelProperty(value: '听课总时长小时', index: 5)]
    public string $course_record_sum_time_hour;

    #[ExcelProperty(value: '登录次数', index: 6)]
    public string $login_count;

    #[ExcelProperty(value: '报名天数', index: 7)]
    public string $order_indate;

    #[ExcelProperty(value: '年级', index: 8)]
    public string $grade_name;

    #[ExcelProperty(value: '报名时间', index: 9)]
    public string $order_created_at;

    #[ExcelProperty(value: '到期时间', index: 10)]
    public string $order_end_date;

    #[ExcelProperty(value: '剩余天数', index: 11)]
    public string $order_end_day;
}
