<?php
namespace App\Order\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 订单管理Dto （导入导出）
 */
#[ExcelData]
class OrderDto implements MineModelExcel
{
    #[ExcelProperty(value: "订单ID", index: 0)]
    public string $id;

    #[ExcelProperty(value: "用户ID", index: 1)]
    public string $user_id;

    #[ExcelProperty(value: "商品ID", index: 2)]
    public string $shop_id;

    #[ExcelProperty(value: "课程ID(套餐子类ID)", index: 3)]
    public string $course_basis_id;

    #[ExcelProperty(value: "商品名称", index: 4)]
    public string $shop_name;

    #[ExcelProperty(value: "课程名称(套餐子类课程名称)", index: 5)]
    public string $course_name;

    #[ExcelProperty(value: "订单编号(用户看,不可随意更改)", index: 6)]
    public string $order_number;

    #[ExcelProperty(value: "支付编号 弃用(使用order_payments 支付单表)", index: 7)]
    public string $pay_number;

    #[ExcelProperty(value: "商品类型:1:课程 2:充值积分 3:图书 4:文库 5:会员 6:面授 7:套餐 8:团购 9:续费", index: 8)]
    public string $shop_type;

    #[ExcelProperty(value: "支付类型:1:微信 2:支付宝 3:虚拟币支付 4:苹果支付 5:学习卡兑换 6:管理员赠送 7:易宝支付 8:优惠券支付 9:亲情卡 10:公益赠送", index: 9)]
    public string $pay_type;

    #[ExcelProperty(value: "订单金额", index: 10)]
    public string $order_price;

    #[ExcelProperty(value: "会员折扣金额", index: 11)]
    public string $vip_discount;

    #[ExcelProperty(value: "优惠券折扣金额", index: 12)]
    public string $coupon_discount;

    #[ExcelProperty(value: "其他折扣金额 拼团", index: 13)]
    public string $other_discount;

    #[ExcelProperty(value: "支付状态:1:未支付 2:已支付 3:已取消 4:已删除 5:退款中 6:已退款 7:已完成", index: 14)]
    public string $pay_states;

    #[ExcelProperty(value: "发货状态 0无需发货 1待发货 2部分发货 3已发货 4已收货", index: 15)]
    public string $ship_status;

    #[ExcelProperty(value: "支付终端: 1:PC,2:安卓,3:IOS,4:H5,5:小程序,6:微信内置H5", index: 16)]
    public string $tag_type;

    #[ExcelProperty(value: "是否赠送:0:不是 1:是", index: 17)]
    public string $is_present;

    #[ExcelProperty(value: "是否发货:0:不发 1:发货", index: 18)]
    public string $is_logistics;

    #[ExcelProperty(value: "评论等级", index: 19)]
    public string $grade;

    #[ExcelProperty(value: "删除时间", index: 20)]
    public string $deleted_at;

    #[ExcelProperty(value: "创建时间", index: 21)]
    public string $created_at;

    #[ExcelProperty(value: "修改时间", index: 22)]
    public string $updated_at;

    #[ExcelProperty(value: "有效期，单位天", index: 23)]
    public string $indate;

    #[ExcelProperty(value: "发货地址id", index: 24)]
    public string $address_id;

    #[ExcelProperty(value: "是否兑换 0不兑换 1.兑换", index: 25)]
    public string $is_exchange;

    #[ExcelProperty(value: "优惠券ID", index: 26)]
    public string $coupon_id;

    #[ExcelProperty(value: "订单备注", index: 27)]
    public string $remark;

    #[ExcelProperty(value: "0不是拼团订单  >0 拼团活动ID", index: 28)]
    public string $spell_id;

    #[ExcelProperty(value: "团ID", index: 29)]
    public string $group_id;

    #[ExcelProperty(value: "班级id,未分班的id为0", index: 30)]
    public string $class_grade_id;

    #[ExcelProperty(value: "是否为线下支付 0:否1:是", index: 31)]
    public string $is_offline;

    #[ExcelProperty(value: "0=无效 1=有效 0暂停 1正常 2退费", index: 32)]
    public string $status;

    #[ExcelProperty(value: "bug_subject", index: 33)]
    public string $bug_subject;

    #[ExcelProperty(value: "bug_subject_name", index: 34)]
    public string $bug_subject_name;

    #[ExcelProperty(value: "有效期类型：0：只能看有效期范围内，1：未知，2：默认，3：有效期到期直播回放都不能看", index: 35)]
    public string $indate_close;

    #[ExcelProperty(value: "audit_status", index: 36)]
    public string $audit_status;

    #[ExcelProperty(value: "update_indate", index: 37)]
    public string $update_indate;

    #[ExcelProperty(value: "is_renew", index: 38)]
    public string $is_renew;

    #[ExcelProperty(value: "activities", index: 39)]
    public string $activities;

    #[ExcelProperty(value: "实际付款金额", index: 40)]
    public string $actual_price;

    #[ExcelProperty(value: "created_name", index: 41)]
    public string $created_name;

    #[ExcelProperty(value: "created_id", index: 42)]
    public string $created_id;

    #[ExcelProperty(value: "cause_text", index: 43)]
    public string $cause_text;

    #[ExcelProperty(value: "是否到期，1：到期", index: 44)]
    public string $is_over;

    #[ExcelProperty(value: "renew_time", index: 45)]
    public string $renew_time;

    #[ExcelProperty(value: "status_time", index: 46)]
    public string $status_time;

    #[ExcelProperty(value: "退费时间", index: 47)]
    public string $refund_time;

    #[ExcelProperty(value: "2980续费关联主订单id", index: 48)]
    public string $renew_order_id;

    #[ExcelProperty(value: "1首月  2正价", index: 49)]
    public string $apply_type;

    #[ExcelProperty(value: "1:普通会员，2:超级会员，3:至尊会员", index: 50)]
    public string $is_vip;

    #[ExcelProperty(value: "用户平台", index: 51)]
    public string $platform;


}