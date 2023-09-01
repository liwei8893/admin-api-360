<?php

declare(strict_types=1);

namespace App\Course\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

#[ExcelData]
class CommentDto implements MineModelExcel
{
    #[ExcelProperty(value: '用户名称', index: 0, customField: 'user.user_name')]
    public string $user_name;

    #[ExcelProperty(value: '手机号', index: 1, customField: 'user.mobile')]
    public string $mobile;

    #[ExcelProperty(value: '课程名称', index: 2, customField: 'course_period.course_basis.title')]
    public string $course_basis_title;

    #[ExcelProperty(value: '章节名称', index: 3, customField: 'course_period.title')]
    public string $course_period_title;

    #[ExcelProperty(value: '内容', index: 4)]
    public string $html;
}
