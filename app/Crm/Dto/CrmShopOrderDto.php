<?php
namespace App\Crm\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 订单管理Dto （导入导出）
 */
#[ExcelData]
class CrmShopOrderDto implements MineModelExcel
{
    #[ExcelProperty(value: "订单 ID，自增主键", index: 0)]
    public string $id;

    #[ExcelProperty(value: "商品 ID，关联商品表", index: 1)]
    public string $shop_id;

    #[ExcelProperty(value: "订单编号，唯一标识", index: 2)]
    public string $order_number;

    #[ExcelProperty(value: "订单金额", index: 3)]
    public string $amount;

    #[ExcelProperty(value: "订单状态，0 - 待付款，1 - 已付款，2 - 已发货，3 - 已完成，4 - 已取消", index: 4)]
    public string $order_status;

    #[ExcelProperty(value: "订单类型", index: 5)]
    public string $order_type;

    #[ExcelProperty(value: "地址信息", index: 6)]
    public string $address_id;

    #[ExcelProperty(value: "物流公司", index: 7)]
    public string $logistics_company;

    #[ExcelProperty(value: "物流单号", index: 8)]
    public string $tracking_number;

    #[ExcelProperty(value: "订单备注", index: 9)]
    public string $order_note;

    #[ExcelProperty(value: "创建人", index: 10)]
    public string $created_id;

    #[ExcelProperty(value: "订单创建时间", index: 11)]
    public string $created_at;

    #[ExcelProperty(value: "订单信息更新时间", index: 12)]
    public string $updated_at;

    #[ExcelProperty(value: "订单删除时间", index: 13)]
    public string $deleted_at;


}