<?php

namespace App\Crm\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 学习记录Dto （导入导出）
 */
#[ExcelData]
class CrmStudyRecordDto implements MineModelExcel
{
    #[ExcelProperty(value: "姓名", index: 0)]
    public string $name;

    #[ExcelProperty(value: "电话", index: 1)]
    public string $phone;

    #[ExcelProperty(value: "辅导老师", index: 3)]
    public string $tutor_teacher;

    #[ExcelProperty(value: "销售老师", index: 4)]
    public string $sales_teacher;

    #[ExcelProperty(value: "主讲老师", index: 6)]
    public string $main_teacher;

    #[ExcelProperty(value: "年级", index: 15)]
    public string $grade;

    #[ExcelProperty(value: "备注", index: 23)]
    public string $remark;

    #[ExcelProperty(value: "课程名称", index: 7)]
    public string $course_name;

    #[ExcelProperty(value: "课次名称", index: 8)]
    public string $lesson_name;

    #[ExcelProperty(value: "进教室时间", index: 10)]
    public string $enter_class_time;

    #[ExcelProperty(value: "离开教室时间", index: 17)]
    public string $leave_class_time;

    #[ExcelProperty(value: "直播时长", index: 11)]
    public string $live_duration;

    #[ExcelProperty(value: "回放时长", index: 12)]
    public string $playback_duration;

    #[ExcelProperty(value: "互动次数", index: 13)]
    public string $interaction_count;


}
