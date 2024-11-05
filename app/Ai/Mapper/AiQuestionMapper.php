<?php
declare(strict_types=1);

namespace App\Ai\Mapper;

use App\Ai\Model\AiQuestion;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractMapper;

/**
 * 题目管理Mapper类
 */
class AiQuestionMapper extends AbstractMapper
{
    /**
     * @var AiQuestion
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = AiQuestion::class;
    }

    /**
     * 随机一道题
     * @param array $params
     * @return AiQuestion|Model|\Hyperf\Database\Query\Builder|Builder|null
     */
    public function randomExam(array $params): AiQuestion|Model|\Hyperf\Database\Query\Builder|Builder|null
    {
        $maxId = AiQuestion::query()->selectRaw('MAX(id)')->whereIn('classify_id', $params);
        $minId = AiQuestion::query()->selectRaw('MIN(id)')->whereIn('classify_id', $params);
        $subModel = Db::raw("select ROUND(RAND() *(({$maxId->toRawSql()})-({$minId->toRawSql()}))+({$minId->toRawSql()})) as id")->getValue();
        return AiQuestion::query()->joinSub($subModel, 't2', function ($join) {
        })
            ->whereIn('classify_id', $params)
            ->whereRaw('ai_question.id >= t2.id')
            ->orderBy('ai_question.id')
            ->first();
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        // 分类ID
        if (isset($params['classify_id']) && $params['classify_id'] !== '') {
            $query->where('classify_id', '=', $params['classify_id']);
        }

        // 年级ID
        if (isset($params['grade_id']) && $params['grade_id'] !== '') {
            $query->where('grade_id', '=', $params['grade_id']);
        }

        // 科目ID
        if (isset($params['subject_id']) && $params['subject_id'] !== '') {
            $query->where('subject_id', '=', $params['subject_id']);
        }

        // 试题类型
        if (isset($params['ques_type']) && $params['ques_type'] !== '') {
            $query->where('ques_type', '=', $params['ques_type']);
        }

        // 试题题目
        if (isset($params['ques_title']) && $params['ques_title'] !== '') {
            $query->where('ques_title', 'like', '%' . $params['ques_title'] . '%');
        }

        // 试题难度:1:易 2:中 3:难
        if (isset($params['ques_difficulty']) && $params['ques_difficulty'] !== '') {
            $query->where('ques_difficulty', '=', $params['ques_difficulty']);
        }

        // 状态 (1正常 0停用)
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', '=', $params['status']);
        }

        // 排序
        if (isset($params['sort']) && $params['sort'] !== '') {
            $query->where('sort', '=', $params['sort']);
        }
        if (!empty($params['withAiKnowsClassify'])) {
            $query->with('knowsClassify');
        }
        return $query;
    }
}
