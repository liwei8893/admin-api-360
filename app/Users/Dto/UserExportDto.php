<?php

declare(strict_types=1);

namespace App\Users\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

#[ExcelData]
class UserExportDto implements MineModelExcel
{
    #[ExcelProperty(value: '用户名', index: 0)]
    public string $user_name;

    #[ExcelProperty(value: '昵称', index: 1)]
    public string $user_nickname;

    #[ExcelProperty(value: '手机号', index: 2)]
    public string $mobile;

    #[ExcelProperty(value: '平台', index: 3)]
    public string $platform;

    #[ExcelProperty(value: '年级', index: 4, customField: 'grades.label')]
    public string $grades;

    #[ExcelProperty(value: '会员类型', index: 5, customField: 'vip_type.vipName')]
    public string $vipType;

    #[ExcelProperty(value: '账号类型', index: 6, customField: 'user_type.label')]
    public string $user_type;

    #[ExcelProperty(value: '账号状态', index: 7, customField: 'status.label')]
    public string $status;

    #[ExcelProperty(value: '创建时间', index: 8)]
    public string $created_at;

    #[ExcelProperty(value: '备注', index: 9)]
    public string $remark;
}
