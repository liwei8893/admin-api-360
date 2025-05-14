<?php
namespace App\Crm\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 用户沟通时间Dto （导入导出）
 */
#[ExcelData]
class CrmUserCommTimelineDto implements MineModelExcel
{
    #[ExcelProperty(value: "主键ID", index: 0)]
    public string $id;

    #[ExcelProperty(value: "用户ID", index: 1)]
    public string $user_id;

    #[ExcelProperty(value: "沟通时间", index: 2)]
    public string $comm_time;

    #[ExcelProperty(value: "沟通内容摘要", index: 3)]
    public string $content;

    #[ExcelProperty(value: "创建时间", index: 4)]
    public string $created_at;

    #[ExcelProperty(value: "更新时间", index: 5)]
    public string $updated_at;


}