<?php

declare(strict_types=1);

namespace App\Order\Model;

use App\Course\Model\CourseBasis;
use App\System\Model\SystemDictData;
use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\Relations\HasMany;
use Mine\MineModel;

/**
 * @property int $id 订单ID
 * @property int $user_id 用户ID
 * @property int $shop_id 商品ID
 * @property int $course_basis_id 课程ID(套餐子类ID)
 * @property string $shop_name 商品名称
 * @property string $course_name 课程名称(套餐子类课程名称)
 * @property string $order_number 订单编号(用户看,不可随意更改)
 * @property string $pay_number 支付编号 弃用(使用order_payments 支付单表)
 * @property int $shop_type 商品类型:1:课程 2:充值积分 3:图书 4:文库 5:会员 6:面授 7:套餐 8:团购 9:续费
 * @property int $pay_type 支付类型:1:微信 2:支付宝 3:虚拟币支付 4:苹果支付 5:学习卡兑换 6:管理员赠送 7:易宝支付 8:优惠券支付 9:亲情卡 10:公益赠送
 * @property int $order_price 订单金额
 * @property int $vip_discount 会员折扣金额
 * @property int $coupon_discount 优惠券折扣金额
 * @property int $other_discount 其他折扣金额 拼团
 * @property int $pay_states 支付状态:1:未支付 2:已支付 3:已取消 4:已删除 5:退款中 6:已退款 7:已完成 8:待审核 9:审核拒绝
 * @property int $ship_status 发货状态 0无需发货 1待发货 2部分发货 3已发货 4已收货
 * @property int $tag_type 支付终端: 1:PC,2:安卓,3:IOS,4:H5,5:小程序,6:微信内置H5
 * @property int $is_present 是否赠送:0:不是 1:是
 * @property int $is_logistics 是否发货:0:不发 1:发货
 * @property int $grade 评论等级
 * @property int $deleted_at 删除时间
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 修改时间
 * @property int $indate 有效期，单位天
 * @property int $address_id 发货地址id
 * @property int $is_exchange 是否兑换 0不兑换 1.兑换
 * @property int $coupon_id 优惠券ID
 * @property string $remark 订单备注
 * @property int $spell_id 0不是拼团订单  >0 拼团活动ID
 * @property int $group_id 团ID
 * @property int $class_grade_id 班级id,未分班的id为0
 * @property int $is_offline 是否为线下支付 0:否1:是
 * @property int $status 0暂停 1正常 2退费
 * @property string $bug_subject
 * @property string $bug_subject_name
 * @property int $indate_close 有效期类型：0：只能看有效期范围内，1：未知，2：默认，3：有效期到期直播回放都不能看
 * @property int $audit_status
 * @property int $update_indate
 * @property int $is_renew
 * @property string $activities
 * @property string $actual_price 实际付款金额
 * @property string $created_name
 * @property int $created_id
 * @property string $cause_text
 * @property int $is_over 是否到期，1：到期
 * @property Carbon $renew_time
 * @property Carbon $status_time
 * @property Carbon $refund_time 退费时间
 * @property int $renew_order_id 2980续费关联主订单id
 * @property int $apply_type 1首月  2正价
 * @property int $is_vip 1:普通会员，2:超级会员，3:至尊会员
 * @property string $platform 用户平台
 * @property string $real_year 真实报名年数
 * @property int $chapter_count_auth 可观看章节数量(0购买课程之后全部都能看)
 * @property int $app 所属应用
 * @property-read string $course_end_time
 * @property-read User|null $users
 * @property-read CourseBasis|null $course
 * @property-read Collection|OrderPayment[]|null $payment
 * @property-read Collection|UsersRenew[]|null $usersRenew
 * @property-read Collection|SystemDictData[]|null $orderSubject
 * @property-read Collection|SystemDictData[]|null $orderGrade
 * @property-read Collection|OrderSummary[]|null $summary
 */
class Order extends MineModel
{
    /**
     *  pay_states 需要审核.
     */
    public const PAY_AUDIT = 8;

    /**
     * pay_states 审核不通过.
     */
    public const PAY_REJECT = 9;

    /**
     *  pay_states 审核通过,报名成功,完成状态.
     */
    public const PAY_SUCCESS = 7;

    /**
     *  status 正常.
     */
    public const STATUS_NORMAL = 1;

    /**
     *  status暂停.
     */
    public const STATUS_PAUSE = 0;

    /**
     *  status退费.
     */
    public const STATUS_REFUND = 2;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'order';

    protected ?string $dateFormat = 'U';

    protected array $fillable = ['id', 'user_id', 'shop_id', 'course_basis_id', 'shop_name', 'course_name', 'order_number', 'pay_number', 'shop_type', 'pay_type', 'order_price', 'vip_discount', 'coupon_discount', 'other_discount', 'pay_states', 'ship_status', 'tag_type', 'is_present', 'is_logistics', 'grade', 'deleted_at', 'created_at', 'updated_at', 'indate', 'address_id', 'is_exchange', 'coupon_id', 'remark', 'spell_id', 'group_id', 'class_grade_id', 'is_offline', 'status', 'bug_subject', 'bug_subject_name', 'indate_close', 'audit_status', 'update_indate', 'is_renew', 'activities', 'actual_price', 'created_name', 'created_id', 'cause_text', 'is_over', 'renew_time', 'status_time', 'refund_time', 'renew_order_id', 'apply_type', 'is_vip', 'platform', 'real_year', 'chapter_count_auth', 'app'];

    // 订单需要审核
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'shop_id' => 'integer', 'course_basis_id' => 'integer', 'shop_type' => 'integer', 'pay_type' => 'integer', 'order_price' => 'integer', 'vip_discount' => 'integer', 'coupon_discount' => 'integer', 'other_discount' => 'integer', 'pay_states' => 'integer', 'ship_status' => 'integer', 'tag_type' => 'integer', 'is_present' => 'integer', 'is_logistics' => 'integer', 'grade' => 'integer', 'deleted_at' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'indate' => 'integer', 'address_id' => 'integer', 'is_exchange' => 'integer', 'coupon_id' => 'integer', 'spell_id' => 'integer', 'group_id' => 'integer', 'class_grade_id' => 'integer', 'is_offline' => 'integer', 'status' => 'integer', 'indate_close' => 'integer', 'audit_status' => 'integer', 'update_indate' => 'integer', 'is_renew' => 'integer', 'actual_price' => 'decimal:2', 'created_id' => 'integer', 'is_over' => 'integer', 'renew_time' => 'datetime', 'status_time' => 'datetime', 'refund_time' => 'datetime', 'renew_order_id' => 'integer', 'apply_type' => 'integer', 'is_vip' => 'integer', 'real_year' => 'decimal:2', 'chapter_count_auth' => 'integer', 'app' => 'integer'];

    // 追加字段
    protected array $appends = ['created_at|indate' => 'course_end_time'];

    protected array $dates = ['created_at', 'updated_at', 'status_time', 'refund_time', 'renew_time'];

    /**
     * 追加字段访问器，订单结束时间.
     */
    public function getCourseEndTimeAttribute(): string
    {
        if (isset($this->attributes['indate'], $this->attributes['created_at'])) {
            return date('Y-m-d H:i:s', strtotime("+{$this->attributes['indate']} day", (int)$this->attributes['created_at']));
        }
        return '';
    }

    /**
     * 反向关联用户表.
     */
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 关联课程表.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(CourseBasis::class, 'shop_id', 'id');
    }

    /**
     * 关联付款表.
     */
    public function payment(): HasMany
    {
        return $this->hasMany(OrderPayment::class, 'order_number', 'order_number')->where('status', 1);
    }

    /**
     * 关联续费表.
     */
    public function usersRenew(): HasMany
    {
        return $this->hasMany(UsersRenew::class, 'order_id', 'id');
    }

    /**
     * 远程关联订单科目.
     */
    public function orderSubject(): BelongsToMany
    {
        return $this->belongsToMany(SystemDictData::class, 'order_subject', 'order_id', 'subject_id', 'id', 'value')
            ->select(['id', 'label as title', 'value as key'])
            ->where('code', 'subject')
            ->where('status', MineModel::ENABLE);
    }

    /**
     * 远程关联订单年级.
     */
    public function orderGrade(): BelongsToMany
    {
        return $this->belongsToMany(SystemDictData::class, 'order_grade', 'order_id', 'grade_id', 'id', 'value')
            ->select(['id', 'label as title', 'value as key'])
            ->where('code', 'grade')
            ->where('status', MineModel::ENABLE);
    }

    /** 关联核单记录 */
    public function summary(): HasMany
    {
        return $this->hasMany(OrderSummary::class, 'order_id', 'id');
    }

    /**
     * 局部作用域,查询订单状态正常的订单.
     */
    public function scopeNormalOrder(Builder $query): Builder
    {
        return $query->where('deleted_at', 0)->where('status', 1)->where('pay_states', 7);
    }

    /**
     * 局部作用域,查询所有状态的未删除的订单.
     */
    public function scopeNoDeleteOrder(Builder $query): Builder
    {
        return $query->where('deleted_at', 0)->where('pay_states', 7);
    }

    /**
     * 查询没过期的订单.
     */
    public function scopeIsNotExpire(Builder $query): Builder
    {
        return $query->whereRaw('((created_at + (indate * 86400)) > unix_timestamp(now()) or app=2)');
    }

    public function scopeVipOrder(Builder $query): Builder
    {
        $subQuery = CourseBasis::query()->select('id')->where('course_title', 64);
        return $query->whereIn('shop_id', $subQuery);
    }

    public function scopeNotVipOrder(Builder $query): Builder
    {
        $subQuery = CourseBasis::query()->select('id')->where('course_title', 64);
        return $query->whereNotIn('shop_id', $subQuery);
    }
}
