<?php
declare(strict_types=1);


namespace App\Question\Mapper;

use App\Question\Model\ExamHistory;
use Carbon\Carbon;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\DbConnection\Model\Model;
use Mine\Abstracts\AbstractMapper;

/**
 * 试卷记录Mapper类
 */
class ExamHistoryMapper extends AbstractMapper
{
    /**
     * @var ExamHistory
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = ExamHistory::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        // 用户ID
        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }

        // 试题ID
        if (isset($params['exam_id']) && $params['exam_id'] !== '') {
            $query->where('exam_id', '=', $params['exam_id']);
        }

        // 0错误；1正确
        if (isset($params['is_right']) && $params['is_right'] !== '') {
            $query->where('is_right', '=', $params['is_right']);
        }

        // 收藏错题本1收藏,0不收藏
        if (isset($params['is_collect']) && $params['is_collect'] !== '') {
            $query->where('is_collect', '=', $params['is_collect']);
        }
        if (!empty($params['withExam'])) {
            $query->with(['exam' => function ($query) {
                $query->with(['examSubject:value,label', 'examType:value,label']);
            }]);
        }
        return $query;
    }

    public function getRanking(array $params = []): Collection
    {
        $params['start_date'] = $params['start_date'] ?? Carbon::now()->startOfMonth();
        $params['end_date'] = $params['end_date'] ?? Carbon::now()->endOfMonth();
        return ExamHistory::query()
            ->with(['users:id,user_name,mobile'])
            ->select(['user_id'])
            ->selectRaw('count(user_id) as num')
            ->whereBetween('created_at', [$params['start_date'], $params['end_date']])
            ->groupBy(['user_id'])
            ->orderBy('num', 'desc')
            ->limit(10)->get();
    }

    public function getRankingMe(int $userId, array $params = []): int
    {
        $params['start_date'] = $params['start_date'] ?? Carbon::now()->startOfMonth();
        $params['end_date'] = $params['end_date'] ?? Carbon::now()->endOfMonth();
        $userNum = ExamHistory::query()
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$params['start_date'], $params['end_date']])
            ->groupBy(['user_id'])
            ->count('user_id');
        $subQuery = ExamHistory::query()
            ->selectRaw('count(user_id) num')
            ->whereBetween('created_at', [$params['start_date'], $params['end_date']])
            ->groupBy(['user_id']);
        return Model::query()->fromSub($subQuery->getQuery(), 't')->where('num', '>=', $userNum)->count();
    }
}
