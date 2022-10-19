<?php

declare (strict_types=1);

namespace App\Score\Model;

use Hyperf\Database\Model\Relations\MorphTo;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $type 1:头像,2头像框,3课程
 * @property int $shop_id type1:avatar,type2:avatar,type3:course_basis
 * @property int $score 兑换需要的积分数
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $deleted_at
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
    protected $casts = ['id' => 'integer', 'shop_type' => 'string', 'shop_id' => 'integer', 'score' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];


    /**
     * 获得拥有此商品的模型。
     * @return MorphTo
     */
    public function shop(): MorphTo
    {
        return $this->morphTo();
    }
}