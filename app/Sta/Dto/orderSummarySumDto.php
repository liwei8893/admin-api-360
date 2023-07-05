<?php

declare(strict_types=1);

namespace App\Sta\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

#[ExcelData]
class orderSummarySumDto implements MineModelExcel
{
    #[ExcelProperty(value: '核单人ID', index: 0)]
    public string $created_id;

    #[ExcelProperty(value: '核单人', index: 1, customField: 'admin_user.nickname')]
    public string $created_name;

    #[ExcelProperty(value: '数量', index: 2)]
    public string $count;
}
