<?php
namespace App\Crm\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 用户地址信息Dto （导入导出）
 */
#[ExcelData]
class CrmUserAddressDto implements MineModelExcel
{
    #[ExcelProperty(value: "地址记录 ID，自增主键", index: 0)]
    public string $id;

    #[ExcelProperty(value: "用户 ID，关联用户表", index: 1)]
    public string $user_id;

    #[ExcelProperty(value: "收货人姓名", index: 2)]
    public string $consignee;

    #[ExcelProperty(value: "收货人联系电话", index: 3)]
    public string $phone;

    #[ExcelProperty(value: "省份", index: 4)]
    public string $province;

    #[ExcelProperty(value: "城市", index: 5)]
    public string $city;

    #[ExcelProperty(value: "区县", index: 6)]
    public string $district;

    #[ExcelProperty(value: "详细地址", index: 7)]
    public string $detail_address;

    #[ExcelProperty(value: "邮政编码", index: 8)]
    public string $postal_code;

    #[ExcelProperty(value: "是否为默认地址，0 表示非默认，1 表示默认", index: 9)]
    public string $is_default;

    #[ExcelProperty(value: "地址创建时间", index: 10)]
    public string $created_at;

    #[ExcelProperty(value: "地址更新时间", index: 11)]
    public string $updated_at;

    #[ExcelProperty(value: "删除时间", index: 12)]
    public string $deleted_at;


}