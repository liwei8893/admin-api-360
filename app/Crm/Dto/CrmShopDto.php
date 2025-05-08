<?php
namespace App\Crm\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 商品管理Dto （导入导出）
 */
#[ExcelData]
class CrmShopDto implements MineModelExcel
{
    #[ExcelProperty(value: "商品 ID，自增主键", index: 0)]
    public string $id;

    #[ExcelProperty(value: "商品名称", index: 1)]
    public string $shop_name;

    #[ExcelProperty(value: "商品分类", index: 2)]
    public string $category_id;

    #[ExcelProperty(value: "商品价格", index: 3)]
    public string $price;

    #[ExcelProperty(value: "商品描述", index: 4)]
    public string $description;

    #[ExcelProperty(value: "商品状态", index: 5)]
    public string $status;

    #[ExcelProperty(value: "商品创建时间", index: 6)]
    public string $created_at;

    #[ExcelProperty(value: "商品信息更新时间", index: 7)]
    public string $updated_at;

    #[ExcelProperty(value: "商品删除时间", index: 8)]
    public string $deleted_at;


}