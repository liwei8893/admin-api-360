<?php

declare(strict_types=1);

namespace App\Order\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 订单管理Dto （导入导出）.
 */
#[ExcelData]
class OrderDto implements MineModelExcel
{
    #[ExcelProperty(value: '订单ID', index: 0)]
    public string $id;

    #[ExcelProperty(value: '课程名称', index: 1)]
    public string $shop_name;

    #[ExcelProperty(value: '用户名称', index: 2, customField: 'users.user_name')]
    public string $users_user_name;

    #[ExcelProperty(value: '手机号', index: 3, customField: 'users.mobile')]
    public string $users_mobile;

    #[ExcelProperty(value: '用户平台', index: 4, customField: 'users.platform')]
    public string $users_platform;

    #[ExcelProperty(value: '用户年级', index: 5, customField: 'users.grade_id', dictName: 'grade')]
    public string $users_grade_id;

    #[ExcelProperty(value: '平台编号', index: 6, customField: 'users.old_platform')]
    public string $users_old_platform;

    #[ExcelProperty(value: '有效期(天)', index: 7)]
    public string $indate;

    #[ExcelProperty(value: '实际付款金额', index: 8)]
    public string $actual_price;

    #[ExcelProperty(value: '创建时间', index: 9)]
    public string $created_at;

    #[ExcelProperty(value: '到期时间', index: 10)]
    public string $course_end_time;

    #[ExcelProperty(value: '微信编号', index: 11)]
    public string $payment_number;

    #[ExcelProperty(value: '支付类型', index: 12)]
    public string $pay_type;

    #[ExcelProperty(value: '支付终端', index: 13)]
    public string $tag_type;

    #[ExcelProperty(value: '状态', index: 14)]
    public string $status;

    #[ExcelProperty(value: '购买年级', index: 15)]
    public string $order_grade;

    #[ExcelProperty(value: '购买科目', index: 16)]
    public string $order_subject;

    #[ExcelProperty(value: '付款链接平台', index: 17)]
    public string $platform;

    #[ExcelProperty(value: '订单备注', index: 18)]
    public string $remark;
}
