<?php

declare(strict_types=1);

namespace App\Question\Model;

use App\System\Model\SystemDictData;
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
 * @property string $ques_option 选项/问题参考答案/填空题：参考答案
 * @property int $empty_nmb 填空数量,只填空题生效
 * @property string $right_answer 正确答案/填空题：答案验证规则1完全一致2仅顺序一致3仅供参考4未设置
 * @property string $ques_analysis 试题解析
 * @property int $ques_difficulty 试题难度:1:易 2:中 3:难
 * @property int $status 状态 (1正常 0停用)
 * @property int $sort 排序
 * @property int $created_by 创建人
 * @property int $updated_by 修改人
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property null|ExamClassify $examClassify
 * @property null|SystemDictData $examGrade
 * @property null|SystemDictData $examSubject
 * @property null|SystemDictData $examType
 */
class Exam extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'exam';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'classify_id', 'grade_id', 'subject_id', 'ques_type', 'ques_title', 'ques_stem', 'ques_stem_text', 'ques_option', 'empty_nmb', 'right_answer', 'ques_analysis', 'ques_difficulty', 'status', 'sort', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'classify_id' => 'integer', 'grade_id' => 'integer', 'subject_id' => 'integer', 'ques_type' => 'integer', 'empty_nmb' => 'integer', 'ques_difficulty' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    // 关联试卷分类表
    public function examClassify(): HasOne
    {
        return $this->hasOne(ExamClassify::class, 'id', 'classify_id');
    }

    // 关联年级
    public function examGrade(): HasOne
    {
        return $this->hasOne(SystemDictData::class, 'value', 'grade_id')
            ->where('code', 'grade')->where('status', MineModel::ENABLE);
    }

    // 关联科目
    public function examSubject(): HasOne
    {
        return $this->hasOne(SystemDictData::class, 'value', 'subject_id')
            ->where('code', 'subject')->where('status', MineModel::ENABLE);
    }

    // 关联题目类型
    public function examType(): HasOne
    {
        return $this->hasOne(SystemDictData::class, 'value', 'ques_type')
            ->where('code', 'questionType')->where('status', MineModel::ENABLE);
    }
}
