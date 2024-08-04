<?php

declare(strict_types=1);

namespace App\Play\Model;

use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property array $board 棋盘
 * @property array $words 提示词
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class PlayIdiom extends MineModel
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'play_idiom';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'board', 'words', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'board' => 'array', 'words' => 'array', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
