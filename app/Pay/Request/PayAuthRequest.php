<?php

declare(strict_types=1);

namespace App\Pay\Request;

use Mine\MineFormRequest;

/**
 * 公众号配置验证数据类.
 */
class PayAuthRequest extends MineFormRequest
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
            // 公众号appid 验证
            'appid' => 'required',
            // 公众号秘钥 验证
            'app_secret' => 'required',
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
            // 公众号appid 验证
            'appid' => 'required',
            // 公众号秘钥 验证
            'app_secret' => 'required',
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
            'appid' => '公众号appid',
            'app_secret' => '公众号秘钥',
        ];
    }
}
