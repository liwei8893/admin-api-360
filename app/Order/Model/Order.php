<?php

declare(strict_types=1);

namespace App\Order\Model;

use App\Course\Model\CourseBasis;
use App\System\Model\SystemDictData;
use App\Users\Model\Users;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\Relations\HasMany;
use Mine\MineModel;

class Order extends MineModel
{
    /**
     * @description pay_states 需要审核.
     */
    public const PAY_AUDIT = 8;

    /**
     * @description pay_states 不需要审核.
     */
    public const PAY_NO_AUDIT = 7;

    /**
     * @description audit_status 审核不通过.
     */
    public const AUDIT_REJECT = 2;

    /**
     * @description audit_status 需要审核.
     */
    public const AUDIT_PENDING = 1;

    /**
     * @description audit_status 不需要审核.
     */
    public const AUDIT_SUCCESS = 0;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order';

    protected $dateFormat = 'U';

    protected $fillable = [
        'id', 'user_id', 'shop_id', 'course_basis_id', 'shop_name', 'course_name', 'order_number', 'pay_number', 'shop_type', 'pay_type', 'order_price', 'vip_discount', 'coupon_discount', 'other_discount', 'pay_states', 'ship_status', 'tag_type', 'is_present', 'is_logistics', 'grade', 'deleted_at', 'created_at', 'updated_at', 'indate', 'address_id', 'is_exchange', 'coupon_id', 'remark', 'spell_id', 'group_id', 'class_grade_id', 'is_offline', 'status', 'bug_subject', 'bug_subject_name', 'indate_close', 'audit_status', 'update_indate', 'is_renew', 'activities', 'actual_price', 'created_name', 'created_id', 'cause_text', 'is_over', 'renew_time', 'status_time', 'refund_time', 'renew_order_id', 'apply_type', 'is_vip', 'platform',
    ];

    // 订单需要审核
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s', 'updated_at' => 'datetime:Y-m-d H:i:s', 'status_time' => 'datetime:Y-m-d H:i:s', 'status' => 'string', 'pay_type' => 'string'];

    // 追加字段
    protected $appends = ['created_at|indate' => 'course_end_time'];

    /**
     * 追加字段访问器，订单结束时间.
     */
    public function getCourseEndTimeAttribute(): string
    {
        if (isset($this->attributes['indate'], $this->attributes['created_at'])) {
            return date('Y-m-d H:i:s', strtotime("+{$this->attributes['indate']} day", $this->attributes['created_at']));
        }
        return '';
    }

    /**
     * 反向关联用户表.
     */
    public function users(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'user_id', 'id');
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
        return $this->hasMany(OrderPayment::class, 'order_number', 'order_number')
            ->where('status', 1);
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
            ->where('code', 'subject')->where('status', MineModel::ENABLE);
    }

    /**
     * 远程关联订单年级.
     */
    public function orderGrade(): BelongsToMany
    {
        return $this->belongsToMany(SystemDictData::class, 'order_grade', 'order_id', 'grade_id', 'id', 'value')
            ->select(['id', 'label as title', 'value as key'])
            ->where('code', 'grade')->where('status', MineModel::ENABLE);
    }

    /**
     * 局部作用域,查询订单状态正常的订单.
     * @param $query
     */
    public function scopeNormalOrder($query)
    {
        return $query->where('deleted_at', 0)->where('status', 1)->whereIn('pay_states', [2, 7]);
    }

    /**
     * 局部作用域,查询所有状态的未删除的订单.
     * @param $query
     */
    public function scopeNoDeleteOrder($query)
    {
        return $query->where('deleted_at', 0)->where('pay_states', 7);
    }

    /**
     * 查询没过期的订单.
     * @param $query
     */
    public function scopeIsNotExpire($query)
    {
        return $query->whereRaw('(created_at + (indate * 86400)) > unix_timestamp(now())');
    }
}
