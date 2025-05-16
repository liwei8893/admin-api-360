<?php
declare(strict_types=1);


namespace App\Crm\Request;

use Mine\MineFormRequest;

/**
 * 订单管理验证数据类
 */
class CrmShopOrderRequest extends MineFormRequest
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
            //商品 ID，关联商品表 验证
            'shop_id' => 'required',
            //订单金额 验证
            'amount' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
        ];
    }


    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => '订单 ID，自增主键',
            'shop_id' => '商品 ID，关联商品表',
            'order_number' => '订单编号，唯一标识',
            'amount' => '订单金额',
            'order_status' => '订单状态，0 - 待付款，1 - 已付款，2 - 已发货，3 - 已完成，4 - 已取消',
            'order_type' => '订单类型',
            'address_id' => '地址信息',
            'logistics_company' => '物流公司',
            'tracking_number' => '物流单号',
            'created_id' => '创建人',
        ];
    }

}
