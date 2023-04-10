<?php

declare(strict_types=1);

namespace App\Order\Request;

use Mine\MineFormRequest;

/**
 * 订单管理验证数据类.
 */
class OrderRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    public function editOrderRules(): array
    {
        return ['orderId' => 'required|integer', 'actual_price' => 'required|integer', 'real_year' => 'required|integer'];
    }

    public function editRenewRules(): array
    {
        return ['id' => 'required|integer', 'money' => 'required|integer', 'real_year' => 'required|integer'];
    }

    public function auditOrderRules(): array
    {
        return ['ids' => 'required|array', 'pay_states' => 'required|integer'];
    }

    public function changeEndDateRules(): array
    {
        return [
            'ids' => 'required|array',
            'renew' => 'required',
            'type' => 'required',
        ];
    }

    public function changeOrderToUserRules(): array
    {
        return [
            'oldUserId' => 'required',
            'newUserId' => 'required',
            'orderId' => 'required',
        ];
    }

    public function changeOrderToRefundRules(): array
    {
        return [
            'orderId' => 'required',
            'userId' => 'required',
            'money' => 'required',
            'remark' => 'required',
        ];
    }

    public function changeOrderToNormalRules(): array
    {
        return [
            'orderId' => 'required',
        ];
    }

    public function changeOrderToPauseRules(): array
    {
        return [
            'orderId' => 'required',
        ];
    }

    /**
     * 新增数据验证规则
     * return array.
     */
    public function saveRules(): array
    {
        return [
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'id' => '订单ID',
            'user_id' => '用户ID',
            'shop_id' => '商品ID',
            'course_basis_id' => '课程ID(套餐子类ID)',
            'shop_name' => '商品名称',
            'course_name' => '课程名称(套餐子类课程名称)',
            'order_number' => '订单编号(用户看,不可随意更改)',
            'pay_number' => '支付编号 弃用(使用order_payments 支付单表)',
            'shop_type' => '商品类型:1:课程 2:充值积分 3:图书 4:文库 5:会员 6:面授 7:套餐 8:团购 9:续费',
            'pay_type' => '支付类型:1:微信 2:支付宝 3:虚拟币支付 4:苹果支付 5:学习卡兑换 6:管理员赠送 7:易宝支付 8:优惠券支付 9:亲情卡 10:公益赠送',
            'order_price' => '订单金额',
            'vip_discount' => '会员折扣金额',
            'coupon_discount' => '优惠券折扣金额',
            'other_discount' => '其他折扣金额 拼团',
            'pay_states' => '支付状态:1:未支付 2:已支付 3:已取消 4:已删除 5:退款中 6:已退款 7:已完成',
            'ship_status' => '发货状态 0无需发货 1待发货 2部分发货 3已发货 4已收货',
            'tag_type' => '支付终端: 1:PC,2:安卓,3:IOS,4:H5,5:小程序,6:微信内置H5',
            'is_present' => '是否赠送:0:不是 1:是',
            'is_logistics' => '是否发货:0:不发 1:发货',
            'grade' => '评论等级',
            'deleted_at' => '删除时间',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
            'indate' => '有效期，单位天',
            'address_id' => '发货地址id',
            'is_exchange' => '是否兑换 0不兑换 1.兑换',
            'coupon_id' => '优惠券ID',
            'remark' => '订单备注',
            'spell_id' => '0不是拼团订单  >0 拼团活动ID',
            'group_id' => '团ID',
            'class_grade_id' => '班级id,未分班的id为0',
            'is_offline' => '是否为线下支付 0:否1:是',
            'status' => '0=无效 1=有效 0暂停 1正常 2退费',
            'bug_subject' => '',
            'bug_subject_name' => '',
            'indate_close' => '有效期类型：0：只能看有效期范围内，1：未知，2：默认，3：有效期到期直播回放都不能看',
            'audit_status' => '',
            'update_indate' => '',
            'is_renew' => '',
            'activities' => '',
            'actual_price' => '实际付款金额',
            'created_name' => '',
            'created_id' => '',
            'cause_text' => '',
            'is_over' => '是否到期，1：到期',
            'renew_time' => '',
            'status_time' => '',
            'refund_time' => '退费时间',
            'renew_order_id' => '2980续费关联主订单id',
            'apply_type' => '1首月  2正价',
            'is_vip' => '1:普通会员，2:超级会员，3:至尊会员',
            'platform' => '用户平台',
        ];
    }
}
