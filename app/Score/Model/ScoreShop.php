<?php

declare(strict_types=1);

namespace App\Score\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Relations\MorphTo;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property string $shop_type 商品模型名称
 * @property int $shop_id 商品ID
 * @property int $score 兑换需要的积分数
 * @property int $sort
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $deleted_at
 * @property Model $shop
 */
class ScoreShop extends MineModel
{
    use SoftDeletes;

    protected $dateFormat = 'U';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'score_shop';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'shop_type', 'shop_id', 'score', 'sort', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'shop_id' => 'integer', 'score' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];

    /**
     * 获得拥有此商品的模型。
     */
    public function shop(): MorphTo
    {
        return $this->morphTo();
    }
}
