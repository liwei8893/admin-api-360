<?php

declare(strict_types=1);

namespace App\Commerce\Model;

use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Mine\MineModel;

/**
 * @property int $id ID
 * @property int $card_id 卡号
 * @property int $user_id 用户ID
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 */
class CommerceCardUsage extends MineModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'commerce_card_usage';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'card_id', 'user_id', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'card_id' => 'integer', 'user_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
