<?php

declare(strict_types=1);

namespace App\Order\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 核单记录Dto （导入导出）.
 */
#[ExcelData]
class OrderSummaryDto implements MineModelExcel
{
    #[ExcelProperty(value: 'id', index: 0)]
    public string $id;

    #[ExcelProperty(value: '用户id', index: 1)]
    public string $user_id;

    #[ExcelProperty(value: '订单ID', index: 2)]
    public string $order_id;

    #[ExcelProperty(value: '用户等级', index: 3)]
    public string $level;

    #[ExcelProperty(value: '是否添加微信', index: 4)]
    public string $has_wechat;

    #[ExcelProperty(value: '是否接通电话', index: 5)]
    public string $has_connect;

    #[ExcelProperty(value: '备注', index: 6)]
    public string $content;

    #[ExcelProperty(value: 'created_at', index: 7)]
    public string $created_at;

    #[ExcelProperty(value: 'updated_at', index: 8)]
    public string $updated_at;

    #[ExcelProperty(value: 'deleted_at', index: 9)]
    public string $deleted_at;
}
