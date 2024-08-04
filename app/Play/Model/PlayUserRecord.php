<?php

declare(strict_types=1);

namespace App\Play\Model;

use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property int $user_id 用户ID
 * @property int $idiom_id 成语接龙关卡等级
 * @property int $word_id 单词游戏最高等级
 * @property int $sudoku_score 数独最高分数
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property-read null|User $user
 */
class PlayUserRecord extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'play_user_record';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'idiom_id', 'word_id', 'sudoku_score', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'idiom_id' => 'integer', 'word_id' => 'integer', 'sudoku_score' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
