<?php

declare(strict_types=1);

namespace App\Question\Mapper;

use App\Question\Model\Exam;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 试卷表Mapper类.
 */
class ExamMapper extends AbstractMapper
{
    /**
     * @var Exam
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Exam::class;
    }

    /**
     * 搜索处理器.
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

        // 试题类型:1:单选题 2:多选题 4:判断题 5:问答题 6:填空题
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

        if (! empty($params['withExamClassify'])) {
            $query->with('examClassify');
        }
        return $query;
    }
}
