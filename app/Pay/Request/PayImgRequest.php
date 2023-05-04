<?php

declare(strict_types=1);

namespace App\Pay\Request;

use Mine\MineFormRequest;

/**
 * 图片配置验证数据类.
 */
class PayImgRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    /**
     * 新增数据验证规则
     * return array.
     */
    public function saveRules(): array
    {
        return [
            // 备注 验证
            'remark' => 'required',
            // 图片地址 验证
            'img' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            // 备注 验证
            'remark' => 'required',
            // 图片地址 验证
            'img' => 'required',
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'remark' => '备注',
            'img' => '图片地址',
        ];
    }
}
