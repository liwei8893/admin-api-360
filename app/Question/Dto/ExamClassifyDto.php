<?php

declare(strict_types=1);

namespace App\Question\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 试卷分类Dto （导入导出）.
 */
#[ExcelData]
class ExamClassifyDto implements MineModelExcel
{
    #[ExcelProperty(value: '主键', index: 0)]
    public string $id;

    #[ExcelProperty(value: '父ID', index: 1)]
    public string $parent_id;

    #[ExcelProperty(value: '组级集合', index: 2)]
    public string $level;

    #[ExcelProperty(value: '菜单名称', index: 3)]
    public string $name;

    #[ExcelProperty(value: '状态 (1正常 0停用)', index: 4)]
    public string $status;

    #[ExcelProperty(value: '排序', index: 5)]
    public string $sort;

    #[ExcelProperty(value: '创建时间', index: 6)]
    public string $created_at;

    #[ExcelProperty(value: '更新时间', index: 7)]
    public string $updated_at;

    #[ExcelProperty(value: '删除时间', index: 8)]
    public string $deleted_at;
}
