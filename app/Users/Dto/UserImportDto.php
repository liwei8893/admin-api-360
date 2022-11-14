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
 * 用户表Dto （导入导出）.
 */
#[ExcelData]
class UserImportDto implements MineModelExcel
{
    #[ExcelProperty(value: '用户名(必填)', index: 0)]
    public string $user_name;

    #[ExcelProperty(value: '手机号(必填)', index: 1)]
    public string $mobile;

    #[ExcelProperty(value: '所属平台编号(大写)', index: 2)]
    public string $platform;

    #[ExcelProperty(value: '年级(必填)', index: 3)]
    public string $grade;

    #[ExcelProperty(value: '备注', index: 4)]
    public string $remark;
}
