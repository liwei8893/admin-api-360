<?php

declare(strict_types=1);

namespace App\Order\Model;

use App\System\Model\SystemUser;
use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\hasOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $user_id 用户id
 * @property int $order_id 订单ID
 * @property int $level 用户等级
 * @property int $has_wechat 是否添加微信
 * @property int $has_connect 是否接通电话
 * @property string $content 备注
 * @property int $created_id 创建人ID
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $deleted_at
 * @property SystemUser $adminUser
 * @property User $user
 * @property Order $order
 */
class OrderSummary extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'order_summary';

    protected ?string $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'order_id', 'level', 'has_wechat', 'has_connect', 'content', 'created_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'order_id' => 'integer', 'level' => 'integer', 'has_wechat' => 'integer', 'has_connect' => 'integer', 'created_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];

    /**
     * 定义 user 关联.
     */
    public function user(): hasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * 定义 order 关联.
     */
    public function order(): hasOne
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function adminUser(): hasOne
    {
        return $this->hasOne(SystemUser::class, 'id', 'created_id');
    }
}
