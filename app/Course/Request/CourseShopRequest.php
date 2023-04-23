<?php

declare(strict_types=1);

namespace App\Course\Request;

use Mine\MineFormRequest;

/**
 * shop验证数据类.
 */
class CourseShopRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    public function getFirstRules(): array
    {
        return ['id' => 'required'];
    }

    /**
     * 新增数据验证规则
     * return array.
     */
    public function saveRules(): array
    {
        return [
            // 0:标题,副标题全部显示,1:只显示标题,2:只显示副标题 验证
            'title_rule' => 'required',
            // 0:不显示,购买之后才显示,1:总是显示,2:会员认证显示 验证
            'show_rule' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            // 0:标题,副标题全部显示,1:只显示标题,2:只显示副标题 验证
            'title_rule' => 'required',
            // 0:不显示,购买之后才显示,1:总是显示,2:会员认证显示 验证
            'show_rule' => 'required',
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'id' => '',
            'title_rule' => '0:标题,副标题全部显示,1:只显示标题,2:只显示副标题',
            'show_rule' => '0:不显示,购买之后才显示,1:总是显示,2:会员认证显示',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
