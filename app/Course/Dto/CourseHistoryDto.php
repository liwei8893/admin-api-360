<?php

declare(strict_types=1);

namespace App\Course\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

#[ExcelData]
class CourseHistoryDto implements MineModelExcel
{
    #[ExcelProperty(value: '用户名称', index: 0, customField: 'users.user_name')]
    public string $title;

    #[ExcelProperty(value: '平台编号', index: 1, customField: 'users.old_platform')]
    public string $old_platform;

    #[ExcelProperty(value: '手机号', index: 2, customField: 'users.mobile')]
    public string $mobile;

    #[ExcelProperty(value: '用户平台', index: 3, customField: 'users.platform')]
    public string $platform;

    #[ExcelProperty(value: '创建时间', index: 4)]
    public string $createdAt;

    #[ExcelProperty(value: '到期时间', index: 5)]
    public string $course_end_time;

    #[ExcelProperty(value: '购买年级', index: 6)]
    public string $orderGrade;

    #[ExcelProperty(value: '购买科目', index: 7)]
    public string $orderSubject;

    #[ExcelProperty(value: '实际付款金额', index: 8)]
    public string $actual_price;

    #[ExcelProperty(value: '有效期(天)', index: 9)]
    public string $indate;

    #[ExcelProperty(value: '有效期(天)', index: 10, customField: 'users.remark')]
    public string $remark;
}
