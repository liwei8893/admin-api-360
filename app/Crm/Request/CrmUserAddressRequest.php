<?php
declare(strict_types=1);


namespace App\Crm\Request;

use Mine\MineFormRequest;

/**
 * 用户地址信息验证数据类
 */
class CrmUserAddressRequest extends MineFormRequest
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
            //收货人姓名 验证
            'consignee' => 'required',
            //收货人联系电话 验证
            'phone' => 'required',
            //省份 验证
            'province' => 'required',
            //城市 验证
            'city' => 'required',
            //区县 验证
            'area' => 'required',
            //详细地址 验证
            'detail_address' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            //收货人姓名 验证
            'consignee' => 'required',
            //收货人联系电话 验证
            'phone' => 'required',
            //省份 验证
            'province' => 'required',
            //城市 验证
            'city' => 'required',
            //区县 验证
            'area' => 'required',
            //详细地址 验证
            'detail_address' => 'required',
        ];
    }


    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => '地址记录 ID，自增主键',
            'user_id' => '用户 ID，关联用户表',
            'consignee' => '收货人姓名',
            'phone' => '收货人联系电话',
            'province' => '省份',
            'city' => '城市',
            'area' => '区县',
            'detail_address' => '详细地址',
            'postal_code' => '邮政编码',

        ];
    }

}
