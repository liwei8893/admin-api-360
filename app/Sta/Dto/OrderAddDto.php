<?php

declare(strict_types=1);

namespace App\Sta\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

#[ExcelData]
class OrderAddDto implements MineModelExcel
{
    #[ExcelProperty(value: '学员姓名', index: 0)]
    public string $user_name;

    #[ExcelProperty(value: '手机号', index: 1)]
    public string $mobile;

    #[ExcelProperty(value: '平台编号', index: 2)]
    public string $platform;

    #[ExcelProperty(value: '购买课程', index: 3)]
    public string $shop_name;

    #[ExcelProperty(value: '下单时间', index: 4)]
    public string $order_created_at;

    #[ExcelProperty(value: '截止时间', index: 5)]
    public string $course_end_time;

    #[ExcelProperty(value: '时长(天)', index: 6)]
    public string $indate;

    #[ExcelProperty(value: '付款金额', index: 7)]
    public string $actual_price;

    #[ExcelProperty(value: '订单备注', index: 8)]
    public string $oRemark;

    #[ExcelProperty(value: '实际报名年数', index: 9)]
    public string $real_year;

    #[ExcelProperty(value: '购买年级', index: 10)]
    public string $order_grade;

    #[ExcelProperty(value: '购买科目', index: 11)]
    public string $order_subject;
}
