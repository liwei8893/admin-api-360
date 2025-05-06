<?php
namespace App\Crm\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 用户时间线记录表Dto （导入导出）
 */
#[ExcelData]
class CrmUserTimelineDto implements MineModelExcel
{
    #[ExcelProperty(value: "ID", index: 0)]
    public string $id;

    #[ExcelProperty(value: "用户ID", index: 1)]
    public string $user_id;

    #[ExcelProperty(value: "创建人id", index: 2)]
    public string $created_by;

    #[ExcelProperty(value: "事件", index: 3)]
    public string $event;

    #[ExcelProperty(value: "事件详情", index: 4)]
    public string $event_detail;

    #[ExcelProperty(value: "创建时间", index: 5)]
    public string $created_at;


}