<?php

declare(strict_types=1);

namespace App\Operation\Model;

use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property string $name 抽奖活动名称
 * @property \Carbon\Carbon $start_time 抽奖活动开始时间
 * @property \Carbon\Carbon $end_time 抽奖活动结束时间
 * @property string $remark
 * @property int $created_by
 * @property int $updated_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class Lottery extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'lottery';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'start_time', 'end_time', 'remark', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'start_time' => 'datetime:Y-m-d H:i:s', 'end_time' => 'datetime:Y-m-d H:i:s', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime:Y-m-d H:i:s', 'updated_at' => 'datetime:Y-m-d H:i:s', 'deleted_at' => 'datetime:Y-m-d H:i:s'];

    protected ?string $dateFormat = 'U';

    protected array $dates = ['start_time', 'end_time', 'created_at', 'updated_at', 'deleted_at'];

    public function lotteryPrize(): HasMany
    {
        return $this->hasMany(LotteryPrize::class, 'lottery_id', 'id');
    }

    public function lotteryUser(): HasMany
    {
        return $this->hasMany(LotteryUser::class, 'lottery_id', 'id');
    }
}
