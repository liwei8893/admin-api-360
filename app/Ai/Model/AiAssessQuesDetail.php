<?php

declare(strict_types=1);

namespace App\Ai\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id ID
 * @property int $assess_report_id 评测报告表ID
 * @property int $user_id 用户ID
 * @property int $ques_id 题目ID
 * @property int $knows_level1_id 1级知识点ID
 * @property string $knows_level1_name 1级知识点名称
 * @property int $knows_level2_id 2级知识点ID
 * @property string $knows_level2_name 2级知识点名称
 * @property int $knows_difficulty 知识点难度
 * @property int $rec_answer_duration 建议答题时间
 * @property array $user_answer 用户答案
 * @property int $is_answer 是否作答
 * @property int $is_right 是否正确
 * @property int $user_answer_duration 答题时间
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property-read AiQuestion|null $question
 * @property-read AiKnowsClassify|null $knowsLevel2
 */
class AiAssessQuesDetail extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'ai_assess_ques_detail';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'assess_report_id', 'user_id', 'ques_id', 'knows_level1_id', 'knows_level1_name', 'knows_level2_id', 'knows_level2_name', 'knows_difficulty', 'rec_answer_duration', 'user_answer', 'is_answer', 'is_right', 'user_answer_duration', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'assess_report_id' => 'integer', 'user_id' => 'integer', 'ques_id' => 'integer', 'knows_level1_id' => 'integer', 'knows_level2_id' => 'integer', 'knows_difficulty' => 'integer', 'rec_answer_duration' => 'integer', 'user_answer' => 'array', 'is_answer' => 'integer', 'is_right' => 'integer', 'user_answer_duration' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function question(): HasOne
    {
        return $this->hasOne(AiQuestion::class, 'id', 'ques_id');
    }

    public function knowsLevel2(): HasOne
    {
        return $this->hasOne(AiKnowsClassify::class, 'id', 'knows_level2_id');
    }
}
