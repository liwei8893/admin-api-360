<?php

namespace App\Users\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 用户下载文件记录Dto （导入导出）
 */
#[ExcelData]
class UserDownloadFileLogDto implements MineModelExcel
{
    #[ExcelProperty(value: "id", index: 0)]
    public string $id;

    #[ExcelProperty(value: "用户ID", index: 1)]
    public string $user_id;

    #[ExcelProperty(value: "章节ID", index: 2)]
    public string $periods_id;

    #[ExcelProperty(value: "文件ID", index: 3)]
    public string $file_id;

    #[ExcelProperty(value: "文件名称", index: 4)]
    public string $file_name;

    #[ExcelProperty(value: "章节名称", index: 5)]
    public string $periods_name;

    #[ExcelProperty(value: "课程名称", index: 6)]
    public string $course_name;

    #[ExcelProperty(value: "下载时间", index: 7)]
    public string $created_at;


}
