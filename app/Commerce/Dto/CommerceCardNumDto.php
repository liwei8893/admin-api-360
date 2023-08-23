<?php

declare(strict_types=1);

namespace App\Commerce\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 电商管理Dto （导入导出）.
 */
#[ExcelData]
class CommerceCardNumDto implements MineModelExcel
{
    #[ExcelProperty(value: '卡号', index: 0)]
    public string $card_id;
}
