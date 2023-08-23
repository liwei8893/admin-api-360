<?php

declare(strict_types=1);

namespace App\Commerce\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 电商卡使用记录Dto （导入导出）.
 */
#[ExcelData]
class CommerceCardUsageDto implements MineModelExcel
{
    #[ExcelProperty(value: 'ID', index: 0)]
    public string $id;

    #[ExcelProperty(value: '卡号', index: 1)]
    public string $card_id;

    #[ExcelProperty(value: '用户名', index: 2, customField: 'user.user_name')]
    public string $user_name;

    #[ExcelProperty(value: '手机号', index: 3, customField: 'user.mobile')]
    public string $mobile;

    #[ExcelProperty(value: '创建时间', index: 4)]
    public string $created_at;
}
