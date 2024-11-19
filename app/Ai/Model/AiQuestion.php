<?php

declare(strict_types=1);

namespace App\Ai\Model;

use App\System\Model\SystemDictData;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property int $classify_id 分类ID
 * @property int $grade_id 年级ID
 * @property int $subject_id 科目ID
 * @property int $ques_type 试题类型:1:单选题 2:多选题 4:判断题 5:问答题 6:填空题
 * @property string $ques_title 试题题目
 * @property string $ques_stem 试题题干
 * @property string $ques_stem_text 文本题干
 * @property array $ques_option 选项/问题参考答案/填空题：参考答案
 * @property int $empty_nmb 填空数量,只填空题生效
 * @property string $right_answer 正确答案/填空题：答案验证规则1完全一致2仅顺序一致3仅供参考4未设置
 * @property string $ques_analysis 试题解析
 * @property int $ques_difficulty 试题难度:1:易 2:中 3:难
 * @property int $status 状态 (1正常 0停用)
 * @property int $sort 排序
 * @property int $created_by 创建人
 * @property int $updated_by 修改人
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property-read AiKnowsClassify|null $knowsClassify
 * @property-read SystemDictData|null $grade
 * @property-read SystemDictData|null $subject
 * @property-read SystemDictData|null $quesType
 */
class AiQuestion extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'ai_question';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'classify_id', 'grade_id', 'subject_id', 'ques_type', 'ques_title', 'ques_stem', 'ques_stem_text', 'ques_option', 'empty_nmb', 'right_answer', 'ques_analysis', 'ques_difficulty', 'status', 'sort', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'classify_id' => 'integer', 'grade_id' => 'integer', 'subject_id' => 'integer', 'ques_type' => 'integer', 'ques_option' => 'array', 'empty_nmb' => 'integer', 'ques_difficulty' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function knowsClassify(): HasOne
    {
        return $this->hasOne(AiKnowsClassify::class, 'id', 'classify_id');
    }

    // 关联年级
    public function grade(): HasOne
    {
        return $this->hasOne(SystemDictData::class, 'value', 'grade_id')
            ->where('code', 'grade')->where('status', MineModel::ENABLE);
    }

    // 关联科目
    public function subject(): HasOne
    {
        return $this->hasOne(SystemDictData::class, 'value', 'subject_id')
            ->where('code', 'subject')->where('status', MineModel::ENABLE);
    }

    // 关联题目类型
    public function quesType(): HasOne
    {
        return $this->hasOne(SystemDictData::class, 'value', 'ques_type')
            ->where('code', 'questionType')->where('status', MineModel::ENABLE);
    }
}
