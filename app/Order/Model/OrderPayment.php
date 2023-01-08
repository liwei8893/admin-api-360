<?php

declare(strict_types=1);

namespace App\Order\Model;

use Mine\MineModel;

/**
 * @property int $payment_id ID
 * @property string $payment_number 支付单编码
 * @property string $order_number 订单号
 * @property string $pay_price 支付金额
 * @property string $pay_app_id 支付方式 wx 微信 alipay 支付宝  balance虚拟币
 * @property int $tag_type 支付终端: 1:PC,2:安卓,3:IOS,4:H5,5:小程序,6:微信内置H5
 * @property string $subject 支付商品名称
 * @property int $user_id 用户ID
 * @property string $trade_no 支付单交易编号
 * @property int $begin_time 开始支付时间
 * @property int $payed_time 支付完成时间
 * @property int $status 支付状态 0准备发起支付 1支付成功 2支付失败 3取消
 */
class OrderPayment extends MineModel
{
    public bool $timestamps = false;

    protected string $primaryKey = 'payment_id';

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'order_payments';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['payment_id', 'payment_number', 'order_number', 'pay_price', 'pay_app_id', 'tag_type', 'subject', 'user_id', 'trade_no', 'begin_time', 'payed_time', 'status'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['payment_id' => 'integer', 'pay_price' => 'decimal:2', 'tag_type' => 'integer', 'user_id' => 'integer', 'begin_time' => 'integer', 'payed_time' => 'integer', 'status' => 'integer'];
}
