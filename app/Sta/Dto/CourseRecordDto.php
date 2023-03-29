<?php

declare(strict_types=1);

namespace App\Sta\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

#[ExcelData]
class CourseRecordDto implements MineModelExcel
{
    #[ExcelProperty(value: '学员姓名', index: 0, customField: 'users.user_name')]
    public string $id;

    #[ExcelProperty(value: '手机号', index: 1, customField: 'users.mobile')]
    public string $mobile;

    #[ExcelProperty(value: '平台编号', index: 2, customField: 'users.platform')]
    public string $platform;

    #[ExcelProperty(value: '课程名称', index: 3, customField: 'courseBasis.title')]
    public string $courseTitle;

    #[ExcelProperty(value: '章节名称', index: 4, customField: 'coursePeriod.title')]
    public string $periodTitle;

    #[ExcelProperty(value: '听课时长(秒)', index: 5)]
    public string $watch_time;

    #[ExcelProperty(value: '视频时长(秒)', index: 6)]
    public string $video_duration;

    #[ExcelProperty(value: '完课率', index: 7)]
    public string $timeRate;

    #[ExcelProperty(value: '报名时间', index: 8)]
    public string $order_created_at;
}
