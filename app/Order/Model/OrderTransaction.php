<?php

declare(strict_types=1);

namespace App\Order\Model;

use App\Users\Model\Users;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $type_id 异动类型
 * @property string $type_name 异动类型 1：退费 2：转班 3转人
 * @property int $order_id 订单ID
 * @property int $user_id 当前用户
 * @property string $money 退款金额
 * @property int $new_user_id 转人后的新学员
 * @property int $new_shop_id 转班后的新课程ID
 * @property string $remark 备注
 * @property Carbon $create_at 创建时间
 * @property int $operator_id 操作人
 * @property int $object_id 关联ID
 * @property int $headmaster_id 退费班主任
 * @property Users $users
 */
class OrderTransaction extends MineModel
{
    /**
     * 退费.
     */
    public const TYPE_REFUND = 1;

    /**
     * 转班.
     */
    public const TYPE_COURSE = 2;

    /**
     * 转人.
     */
    public const TYPE_USER = 3;

    public $timestamps = false;

    /**
     * The table associated with the model.
     * 订单变更记录表.
     * @var string
     */
    protected $table = 'order_transaction';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'type_id', 'type_name', 'order_id', 'user_id', 'money', 'new_user_id', 'new_shop_id', 'remark', 'create_at', 'operator_id', 'object_id', 'headmaster_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'type_id' => 'integer', 'order_id' => 'integer', 'user_id' => 'integer', 'money' => 'decimal:2', 'new_user_id' => 'integer', 'new_shop_id' => 'integer', 'create_at' => 'datetime', 'operator_id' => 'integer', 'object_id' => 'integer', 'headmaster_id' => 'integer'];

    protected $dates = ['create_at'];

    /**
     * 关联用户表.
     */
    public function users(): HasOne
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }
}
