<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Users\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 听课记录Dto （导入导出）.
 */
#[ExcelData]
class UserCourseRecordDto implements MineModelExcel
{
    #[ExcelProperty(value: '课程名称', index: 0, customField: 'course_basis.title')]
    public string $course_basis;

    #[ExcelProperty(value: '章节名称', index: 1, customField: 'course_period.title')]
    public string $course_period;

    #[ExcelProperty(value: '听课时间', index: 2)]
    public string $watch_time;

    #[ExcelProperty(value: '课程总时间', index: 3)]
    public string $video_duration;

    #[ExcelProperty(value: '听课进度', index: 4)]
    public string $timeRate;

    #[ExcelProperty(value: '首次听课时间', index: 5)]
    public string $created_at;
}
