<?php

declare(strict_types=1);

namespace App\System\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 标签管理Dto （导入导出）.
 */
#[ExcelData]
class TagsDto implements MineModelExcel
{
    #[ExcelProperty(value: 'id', index: 0)]
    public string $id;

    #[ExcelProperty(value: '标签名称', index: 1)]
    public string $name;

    #[ExcelProperty(value: '标签状态 0:禁用 1:正常', index: 2)]
    public string $status;

    #[ExcelProperty(value: '创建者', index: 3)]
    public string $created_by;

    #[ExcelProperty(value: '更新者', index: 4)]
    public string $updated_by;

    #[ExcelProperty(value: 'created_at', index: 5)]
    public string $created_at;

    #[ExcelProperty(value: 'updated_at', index: 6)]
    public string $updated_at;

    #[ExcelProperty(value: 'deleted_at', index: 7)]
    public string $deleted_at;
}
