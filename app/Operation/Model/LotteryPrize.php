<?php

declare(strict_types=1);

namespace App\Operation\Model;

use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property string $name
 * @property int $type 1:实物奖品 2:虚拟奖品 3:积分奖品 4:优惠券奖品 5:红包奖品
 * @property int $lottery_id 关联抽奖ID
 * @property int $num 奖品数量
 * @property int $last_num 剩余数量
 * @property float $rate 获奖概率百分之
 * @property string $remark
 * @property int $created_by
 * @property int $updated_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $deleted_at
 */
class LotteryPrize extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'lottery_prize';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'type', 'lottery_id', 'num', 'last_num', 'rate', 'remark', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'type' => 'integer', 'lottery_id' => 'integer', 'num' => 'integer', 'last_num' => 'integer', 'rate' => 'float', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime:Y-m-d H:i:s', 'updated_at' => 'datetime:Y-m-d H:i:s', 'deleted_at' => 'integer'];

    protected ?string $dateFormat = 'U';
}
