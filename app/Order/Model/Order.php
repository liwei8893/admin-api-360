<?php
declare (strict_types=1);

namespace App\Order\Model;

use App\Users\Model\Users;
use Mine\MineModel;

class Order extends MineModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order';
    protected $dateFormat = 'U';
    protected $fillable = [
        'id', 'user_id', 'shop_id', 'course_basis_id', 'shop_name', 'course_name', 'order_number', 'pay_number', 'shop_type', 'pay_type', 'order_price', 'vip_discount', 'coupon_discount', 'other_discount', 'pay_states', 'ship_status', 'tag_type', 'is_present', 'is_logistics', 'grade', 'deleted_at', 'created_at', 'updated_at', 'indate', 'address_id', 'is_exchange', 'coupon_id', 'remark', 'spell_id', 'group_id', 'class_grade_id', 'is_offline', 'status', 'bug_subject', 'bug_subject_name', 'indate_close', 'audit_status', 'update_indate', 'is_renew', 'activities', 'actual_price', 'created_name', 'created_id', 'cause_text', 'is_over', 'renew_time', 'status_time', 'refund_time', 'renew_order_id', 'apply_type', 'is_vip', 'platform'
    ];
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s', 'updated_at' => 'datetime:Y-m-d H:i:s', 'status_time' => 'datetime:Y-m-d H:i:s'];
    protected $appends = ['created_at|indate' => 'course_end_time'];

    /**
     * 追加字段访问器，订单结束时间
     * @return string
     * author:ZQ
     * time:2021-06-15 16:05
     */
    public function getCourseEndTimeAttribute(): string
    {
        if (isset($this->attributes['indate'], $this->attributes['created_at'])) {
            return date('Y-m-d H:i:s', strtotime("+{$this->attributes['indate']} day", $this->attributes['created_at']));
        }
        return '';
    }

    /**
     * 反向关联用户表
     * @return \Hyperf\Database\Model\Relations\BelongsTo
     * author:ZQ
     * time:2022-05-29 16:57
     */
    public function users(): \Hyperf\Database\Model\Relations\BelongsTo
    {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }

    /**
     * 局部作用域,查询订单状态正常的订单
     * author:ZQ
     * time:2022-05-29 17:09
     */
    public function scopeNormalOrder($query)
    {
        return $query->where('deleted_at', 0)->where('status', 1)->whereIn('pay_states', [2, 7]);
    }

    /**
     * 局部作用域,查询所有状态的未删除的订单
     * author:ZQ
     * time:2022-05-29 17:09
     */
    public function scopeNoDeleteOrder($query)
    {
        return $query->where('deleted_at', 0)->whereIn('pay_states', [2, 7]);
    }

    /**
     * 查询没过期的订单
     * @param $query
     * @return mixed
     * author:ZQ
     * time:2022-06-01 09:42
     */
    public function scopeIsNotExpire($query): mixed
    {
        return $query->whereRaw('(created_at + (indate * 86400)) > unix_timestamp(now())');
    }
}