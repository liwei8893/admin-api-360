<?php

declare (strict_types=1);

namespace App\Score\Model;

use App\Users\Model\Users;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\Relations\MorphOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $type 1头像,2头像框
 * @property string $url 头像地址
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $deleted_at
 */
class Avatar extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'avatar';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'url', 'sort', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'type' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];

    /**
     * 多态一对一关联积分商品表
     * @return MorphOne
     */
    public function scoreShop(): MorphOne
    {
        return $this->morphOne(ScoreShop::class, 'shop');
    }

    /**
     * 关联用户表
     * @return BelongsToMany
     */
    public function usersTable(): BelongsToMany
    {
        return $this->belongsToMany(Users::class, 'user_avatar', 'avatar_id', 'user_id');
    }
}