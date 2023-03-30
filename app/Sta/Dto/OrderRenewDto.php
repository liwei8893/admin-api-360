<?php

declare(strict_types=1);

namespace App\Sta\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

#[ExcelData]
class OrderRenewDto implements MineModelExcel
{
    #[ExcelProperty(value: '学员姓名', index: 0, customField: 'users.user_name')]
    public string $user_name;

    #[ExcelProperty(value: '手机号', index: 1, customField: 'users.mobile')]
    public string $mobile;

    #[ExcelProperty(value: '平台编号', index: 2, customField: 'users.platform')]
    public string $platform;

    #[ExcelProperty(value: '购买课程', index: 3, customField: 'order.shop_name')]
    public string $shop_name;

    #[ExcelProperty(value: '下单时间', index: 4)]
    public string $created_at;

    #[ExcelProperty(value: '起始日期', index: 5)]
    public string $indate_start;

    #[ExcelProperty(value: '截止时间', index: 6)]
    public string $indate_end;

    #[ExcelProperty(value: '时长(天)', index: 7)]
    public string $renew_day;

    #[ExcelProperty(value: '付款金额', index: 8)]
    public string $money;

    #[ExcelProperty(value: '续费类型', index: 9)]
    public string $status;

    #[ExcelProperty(value: '实际报名年数', index: 10)]
    public string $real_year;

    #[ExcelProperty(value: '备注', index: 11)]
    public string $remark;

    #[ExcelProperty(value: '购买年级', index: 12)]
    public string $order_grade;

    #[ExcelProperty(value: '购买科目', index: 13)]
    public string $order_subject;
}
