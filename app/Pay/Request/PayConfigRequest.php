<?php

declare(strict_types=1);

namespace App\Pay\Request;

use Mine\MineFormRequest;

/**
 * 商户配置验证数据类.
 */
class PayConfigRequest extends MineFormRequest
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
            // 公众号 APPID 验证
            'app_id' => 'required',
            // 商户号 验证
            'mch_id' => 'required',
            // 商户秘钥 验证
            'key' => 'required',
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
            // 公众号 APPID 验证
            'app_id' => 'required',
            // 商户号 验证
            'mch_id' => 'required',
            // 商户秘钥 验证
            'key' => 'required',
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
            'app_id' => '公众号 APPID',
            'mch_id' => '商户号',
            'key' => '商户秘钥',
        ];
    }
}
