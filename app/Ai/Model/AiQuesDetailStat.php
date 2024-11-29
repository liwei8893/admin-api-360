<?php

declare(strict_types=1);

namespace App\Ai\Model;

use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id ID
 * @property int $ques_id 题目ID
 * @property int $total_user_count 总做题人数
 * @property int $ques_correct_count 正确题目数
 * @property int $ques_incorrect_count 错误题目数
 * @property string $ques_correct_rate 题目正确率
 * @property int $avg_answer_duration 平均答题时间
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class AiQuesDetailStat extends MineModel
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'ai_ques_detail_stat';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'ques_id', 'total_user_count', 'ques_correct_count', 'ques_incorrect_count', 'ques_correct_rate', 'avg_answer_duration', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'ques_id' => 'integer', 'total_user_count' => 'integer', 'ques_correct_count' => 'integer', 'ques_incorrect_count' => 'integer', 'ques_correct_rate' => 'decimal:2', 'avg_answer_duration' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
