<?php

declare(strict_types=1);

namespace App\Ai\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id ID
 * @property int $user_id 用户ID
 * @property array $knows_id 知识点ID
 * @property int $difficulty 难度,1-3
 * @property int $grade 年级
 * @property int $subject 科目
 * @property int $is_assess_done 是否完成评测:1完成,0未完成
 * @property int $knows_count 知识点总数
 * @property int $knows_mastered_count 已掌握知识点数量
 * @property int $knows_unmastered_count 未掌握知识点数量
 * @property string $knows_mastered_rate 知识点掌握率
 * @property int $ques_count 题目总数
 * @property int $ques_correct_count 正确题目数
 * @property int $ques_incorrect_count 错误题目数
 * @property string $ques_correct_rate 题目正确率
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property-read Collection|AiAssessQuesDetail[]|null $quesDetail
 */
class AiAssessReport extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'ai_assess_report';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'knows_id', 'difficulty', 'grade', 'subject', 'is_assess_done', 'knows_count', 'knows_mastered_count', 'knows_unmastered_count', 'knows_mastered_rate', 'ques_count', 'ques_correct_count', 'ques_incorrect_count', 'ques_correct_rate', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'knows_id' => 'array', 'difficulty' => 'integer', 'grade' => 'integer', 'subject' => 'integer', 'is_assess_done' => 'integer', 'knows_count' => 'integer', 'knows_mastered_count' => 'integer', 'knows_unmastered_count' => 'integer', 'knows_mastered_rate' => 'decimal:2', 'ques_count' => 'integer', 'ques_correct_count' => 'integer', 'ques_incorrect_count' => 'integer', 'ques_correct_rate' => 'decimal:2', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function quesDetail(): HasMany
    {
        return $this->hasMany(AiAssessQuesDetail::class, 'assess_report_id', 'id');
    }
}
