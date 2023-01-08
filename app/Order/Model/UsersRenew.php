<?php

declare(strict_types=1);

namespace App\Order\Model;

use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $order_id
 * @property Carbon $indate_start
 * @property Carbon $indate_end
 * @property Carbon $created_at
 * @property int $created_id
 * @property int $status
 * @property string $money
 * @property string $created_name
 * @property int $shop_id
 * @property int $user_id
 * @property int $audit_status
 * @property string $remark
 * @property string $cause_text
 * @property int $renew_experience 续费时属性
 * @property string $in_date
 */
class UsersRenew extends MineModel
{
    /**
     * 续费.
     * @description 续费
     */
    public const STATUS_RENEW = 1;

    /**
     * @description 修改有效期
     */
    public const STATUS_CHANGE = 0;

    public bool $timestamps = false;

    /**
     * The table associated with the model.
     * 续费表.
     */
    protected ?string $table = 'users_renew';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'order_id', 'indate_start', 'indate_end', 'created_at', 'created_id', 'status', 'money', 'created_name', 'shop_id', 'user_id', 'audit_status', 'remark', 'cause_text', 'renew_experience'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'order_id' => 'integer', 'indate_start' => 'datetime', 'indate_end' => 'datetime', 'created_at' => 'datetime', 'created_id' => 'integer', 'status' => 'integer', 'money' => 'decimal:2', 'shop_id' => 'integer', 'user_id' => 'integer', 'audit_status' => 'integer', 'renew_experience' => 'integer'];

    protected array $dates = ['indate_start', 'indate_end', 'created_at'];

    // 追加字段
    protected array $appends = ['indate_start|indate_end' => 'renew_day'];

    /**
     * 追加字段访问器，续费天数.
     */
    public function getRenewDayAttribute(): int
    {
        if (isset($this->attributes['indate_start'], $this->attributes['indate_end'])) {
            return $this->indate_end->diffInDays($this->indate_start);
        }
        return 0;
    }

    /**
     * 关联用户表.
     */
    public function users(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
