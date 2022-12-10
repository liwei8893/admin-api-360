<?php

declare(strict_types=1);

namespace App\Question\Model;

use App\System\Model\SystemDictData;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Mine\MineModel;

/**
 * @property int $id 试题ID
 * @property string $knows_id 知识点ID
 * @property int $classify_id 分类ID
 * @property int $parent_id 父级ID
 * @property int $channel 试题来源 默认1：每日一题 ，2：入学测试，3：视频解析
 * @property int $semester 学期 1 春季 2秋季
 * @property int $ques_type 试题类型:1:单选题 2:多选题 3:不定项选择 4:判断题 5:问答题 6:填空题 7:组合题
 * @property string $ques_title
 * @property string $ques_stem 试题题干
 * @property string $ques_stem_text 文本题干
 * @property string $ques_option 选项/问题参考答案/填空题：参考答案
 * @property string $right_answer 正确答案/填空题：答案验证规则1完全一致2仅顺序一致3仅供参考4未设置
 * @property string $ques_analysis 试题解析
 * @property int $ques_difficulty 试题难度:1:易 2:中 3:难
 * @property int $sort 排序
 * @property int $states 状态:0:显示 1:隐藏
 * @property int $deleted_at 删除时间
 * @property int $created_id 创建人
 * @property Carbon $created_at 创建时间
 * @property int $updated_id 修改人
 * @property Carbon $updated_at 修改时间
 * @property int $form_at 日期
 * @property string $knows_text 知识点文本
 */
class Question extends MineModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'question';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'knows_id', 'classify_id', 'parent_id', 'channel', 'semester', 'ques_type', 'ques_title', 'ques_stem', 'ques_stem_text', 'ques_option', 'right_answer', 'ques_analysis', 'ques_difficulty', 'sort', 'states', 'deleted_at', 'created_id', 'created_at', 'updated_id', 'updated_at', 'form_at', 'knows_text'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'classify_id' => 'integer', 'parent_id' => 'integer', 'channel' => 'integer', 'semester' => 'integer', 'ques_type' => 'integer', 'ques_difficulty' => 'integer', 'sort' => 'integer', 'states' => 'integer', 'deleted_at' => 'integer', 'created_id' => 'integer', 'created_at' => 'datetime:Y-m-d H:i:s', 'updated_id' => 'integer', 'updated_at' => 'datetime:Y-m-d H:i:s', 'form_at' => 'integer'];

    protected $dateFormat = 'U';

    /**
     * 题目科目.
     * @return HasOne
     */
    public function questionSubject(): HasOne
    {
        return $this->hasOne(SystemDictData::class, 'value', 'classify_id')
            ->where('code', 'questionSubject')->where('status', MineModel::ENABLE);
    }

    /**
     * 题目类型,单选多选填空.
     * @return HasOne
     */
    public function questionType(): HasOne
    {
        return $this->hasOne(SystemDictData::class, 'value', 'ques_type')
            ->where('code', 'questionType')->where('status', MineModel::ENABLE);
    }

    /**
     * 知识点表.
     * @return HasOne
     */
    public function knows(): HasOne
    {
        return $this->hasOne(Know::class, 'id', 'knows_id')
            ->where('deleted_at', 0)
            ->where('status', 1);
    }
}
