<?php

declare(strict_types=1);

namespace App\Users\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 用户备注Dto （导入导出）.
 */
#[ExcelData]
class UserRemarkDto implements MineModelExcel
{
    #[ExcelProperty(value: 'id', index: 0)]
    public string $id;

    #[ExcelProperty(value: '用户名', index: 1, customField: 'user.user_name')]
    public string $user_name;

    #[ExcelProperty(value: '手机号', index: 2, customField: 'user.mobile')]
    public string $mobile;

    #[ExcelProperty(value: '平台', index: 3, customField: 'user.platform')]
    public string $platform;

    #[ExcelProperty(value: '备注类型', index: 4)]
    public string $type;

    #[ExcelProperty(value: '售后类型', index: 5)]
    public string $after_sale_type;

    #[ExcelProperty(value: '售后状态', index: 6)]
    public string $has_completed;

    #[ExcelProperty(value: '备注', index: 7)]
    public string $remark;

    #[ExcelProperty(value: '创建人', index: 8, customField: 'admin_user.nickname')]
    public string $created_id;

    #[ExcelProperty(value: '创建时间', index: 9)]
    public string $created_at;

    #[ExcelProperty(value: '更新时间', index: 10)]
    public string $updated_at;
}
