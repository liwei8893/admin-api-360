<?php

declare(strict_types=1);

namespace App\Score\Model;

use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\Relations\MorphOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $type 1头像,2头像框
 * @property string $url 头像地址
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $deleted_at
 */
class Avatar extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'avatar';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['type', 'url', 'sort', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'type' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];

    /**
     * 多态一对一关联积分商品表.
     */
    public function scoreShop(): MorphOne
    {
        return $this->morphOne(ScoreShop::class, 'shop');
    }

    /**
     * 关联用户表.
     */
    public function usersTable(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_avatar', 'avatar_id', 'user_id');
    }
}
