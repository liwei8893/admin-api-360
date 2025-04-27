<?php
namespace App\CRM\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 话单记录Dto （导入导出）
 */
#[ExcelData]
class CrmCallRecordDto implements MineModelExcel
{
    #[ExcelProperty(value: "ID", index: 0)]
    public string $id;

    #[ExcelProperty(value: "坐席号码，仅API自动外呼有此参数", index: 1)]
    public string $caller;

    #[ExcelProperty(value: "被叫号码", index: 2)]
    public string $callee;

    #[ExcelProperty(value: "自动外呼任务ID，仅API自动外呼有此参数", index: 3)]
    public string $task_id;

    #[ExcelProperty(value: "状态码，1为呼叫成功，0为呼叫失败,2为呼叫中", index: 4)]
    public string $status;

    #[ExcelProperty(value: "挂断方信息、呼叫状态信息和SIP响应状态码，中间用英文逗号隔开，辅助排查故障", index: 5)]
    public string $status_info;

    #[ExcelProperty(value: "通话时长，大于等于0的整数，单位为秒", index: 6)]
    public string $duration;

    #[ExcelProperty(value: "通话唯一标识。", index: 7)]
    public string $return_uuid;

    #[ExcelProperty(value: "录音地址，记录到CRM系统的通话记录，点击可以播放。呼叫失败则为空", index: 8)]
    public string $record_url;

    #[ExcelProperty(value: "执行呼叫的时间戳", index: 9)]
    public string $create_time;

    #[ExcelProperty(value: "时间戳", index: 10)]
    public string $api_date;


}