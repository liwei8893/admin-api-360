<?php

declare(strict_types=1);

namespace App\Play\Model;

use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property string $word 单词
 * @property string $uk 英式英标
 * @property string $uk_speech 英式发音
 * @property string $us 美式英标
 * @property string $us_speech 美式发音
 * @property array $trs 中文翻译
 * @property array $word_card 卡片单词
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class PlayWord extends MineModel
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'play_word';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'word', 'uk', 'uk_speech', 'us', 'us_speech', 'trs', 'word_card', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'trs' => 'array', 'word_card' => 'array', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
