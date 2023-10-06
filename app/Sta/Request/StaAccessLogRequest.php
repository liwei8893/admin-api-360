<?php

declare(strict_types=1);

namespace App\Sta\Request;

use Mine\MineFormRequest;

class StaAccessLogRequest extends MineFormRequest
{
    public function setAccessLogRules(): array
    {
        return [
            'page' => 'required|string|in:首页,小学版,初中版,高中版,优品,学习中心',
            'device' => 'required|integer|in:1,2',
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'device' => '设备1:pc,2:h5',
            'page' => '页面',
        ];
    }
}
