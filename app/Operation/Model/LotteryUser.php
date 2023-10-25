<?php

declare(strict_types=1);

namespace App\Operation\Model;

use App\Users\Model\User;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $user_id
 * @property int $lottery_prize_id 奖品ID
 * @property int $lottery_id 抽奖ID
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $deleted_at
 * @property null|User $user
 * @property null|LotteryPrize $lotteryPrize
 */
class LotteryUser extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'lottery_user';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'lottery_prize_id', 'lottery_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'lottery_prize_id' => 'integer', 'lottery_id' => 'integer', 'created_at' => 'datetime:Y-m-d H:i:s', 'updated_at' => 'datetime:Y-m-d H:i:s', 'deleted_at' => 'integer'];

    protected ?string $dateFormat = 'U';

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function lotteryPrize(): HasOne
    {
        return $this->hasOne(LotteryPrize::class, 'id', 'lottery_prize_id');
    }
}
