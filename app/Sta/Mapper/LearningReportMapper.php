<?php

declare(strict_types=1);

namespace App\Sta\Mapper;

use App\Question\Model\QuestionHistory;
use App\Users\Model\UserCourseRecord;
use Hyperf\Collection\Collection;
use Mine\Abstracts\AbstractMapper;

class LearningReportMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        // TODO: Implement assignModel() method.
    }

    /**
     * 看课数量.
     * @param mixed $params
     */
    public function getLearningCourseCount(array $params): UserCourseRecord
    {
        return UserCourseRecord::query()
            ->selectRaw('IFNULL(FLOOR(sum(watch_time) / 60), 0) as minute')
            ->selectRaw('count(*) count')
            ->where('user_id', $params['user_id'])
            ->whereBetween('created_at', [$params['start_time'], $params['end_time']])
            ->first();
    }

    public function getQuestionOfSubjectCount(array $params): array|\Hyperf\Database\Model\Collection|Collection
    {
        return QuestionHistory::query()
            ->from('question_history as qh')
            ->leftJoin('question as q', 'q.id', 'ques_id')
            ->leftJoin('system_dict_data as dd', function ($join) {
                $join->on('dd.value', '=', 'q.classify_id')
                    ->where('dd.code', '=', 'questionSubject')
                    ->where('status', 1);
            })
            ->where('user_id', $params['user_id'])
            ->whereBetween('qh.created_at', [$params['start_time'], $params['end_time']])
            ->select(['classify_id', 'dd.label'])
            ->selectRaw('count(*) as count')
            ->groupBy(['classify_id', 'dd.label'])
            ->get();
    }

    public function getQuestionObjectiveRate(array $params): array|\Hyperf\Database\Model\Collection|Collection
    {
        return QuestionHistory::query()
            ->from('question_history as qh')
            ->leftJoin('question as q', 'q.id', 'ques_id')
            ->leftJoin('system_dict_data as dd', function ($join) {
                $join->on('dd.value', '=', 'q.classify_id')
                    ->where('dd.code', '=', 'questionSubject')
                    ->where('status', 1);
            })
            ->where('user_id', $params['user_id'])
            ->whereBetween('qh.created_at', [$params['start_time'], $params['end_time']])
            ->whereIn('q.ques_type', [1, 2, 4])
            ->select(['classify_id', 'dd.label'])
            ->selectRaw('count(*)                                AS count')
            ->selectRaw('SUM(IF(is_right = 1, 1, 0))             AS right_count')
            ->selectRaw('SUM(IF(is_right = 0, 1, 0))             AS err_count')
            ->selectRaw('FLOOR((sum(is_right) / count(*)) * 100) AS rate')
            ->groupBy(['classify_id', 'dd.label'])
            ->get();
    }
}
