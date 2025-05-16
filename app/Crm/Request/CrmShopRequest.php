<?php
declare(strict_types=1);

namespace App\Crm\Request;

use Mine\MineFormRequest;

/**
 * 商品管理验证数据类
 */
class CrmShopRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [];
    }


    /**
     * 新增数据验证规则
     * return array
     */
    public function saveRules(): array
    {
        return [
            //商品名称 验证
            'shop_name' => 'required',
            //商品分类 验证
            'category_id' => 'required',
            //商品价格 验证
            'price' => 'required',
            //商品状态 验证
            'status' => 'required',

        ];
    }

    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            //商品名称 验证
            'shop_name' => 'required',
            //商品分类 验证
            'category_id' => 'required',
            //商品价格 验证
            'price' => 'required',
            //商品状态 验证
            'status' => 'required',

        ];
    }


    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => '商品 ID，自增主键',
            'shop_name' => '商品名称',
            'category_id' => '商品分类',
            'price' => '商品价格',
            'status' => '商品状态',

        ];
    }

}
