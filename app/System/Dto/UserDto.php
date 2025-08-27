<?php

declare(strict_types=1);

namespace App\System\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 用户DTO.
 */
#[ExcelData]
class UserDto implements MineModelExcel
{
    #[ExcelProperty(value: '用户名', index: 0)]
    public string $username;

    #[ExcelProperty(value: '昵称', index: 1)]
    public string $nickname;

    #[ExcelProperty(value: '手机', index: 2)]
    public string $phone;

    #[ExcelProperty(value: '角色', index: 3)]
    public string $role_name;

    #[ExcelProperty(value: '所属部门', index: 4)]
    public string $dept_name;

    #[ExcelProperty(value: '状态', index: 5, dictName: 'data_status')]
    public string $status;
}
