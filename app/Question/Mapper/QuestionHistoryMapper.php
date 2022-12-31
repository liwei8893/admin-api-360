<?php

declare(strict_types=1);

namespace App\Question\Mapper;

use App\Question\Model\QuestionHistory;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\DbConnection\Db;
use Hyperf\DbConnection\Model\Model;
use Mine\Abstracts\AbstractMapper;

/**
 * 错题表Mapper类.
 */
class QuestionHistoryMapper extends AbstractMapper
{
    /**
     * @var QuestionHistory
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = QuestionHistory::class;
    }

    public function firstModelByUserAndId($params): \Hyperf\Database\Model\Model|Builder|null
    {
        return QuestionHistory::query()->where('user_id', $params['userId'])->where('id', $params['id'])->first();
    }

    public function getRanking(): Collection|array
    {
        return QuestionHistory::query(true)
            ->with(['users:id,user_name,mobile'])
            ->select(['user_id'])
            ->selectRaw('count(user_id) as num')
            ->groupBy(['user_id'])
            ->orderBy('num', 'desc')
            ->limit(10)->get();
    }

    public function getRankingMe(): int
    {
        $userId = user('app')->getId();
        $userNum = QuestionHistory::query()->where('user_id', $userId)->groupBy(['user_id'])->count('user_id');
        $subQuery = QuestionHistory::query()->selectRaw('count(user_id) num')->groupBy(['user_id']);
        return Model::query()->fromSub($subQuery->getQuery(), 't')->where('num', '>=', $userNum)->count();
    }

    public function getRankingRate(): float
    {
        $userRanking = $this->getRankingMe();
        $subQuery = QuestionHistory::query()->selectRaw('count(user_id) num')->groupBy(['user_id']);
        $totalRanking = Model::query()->fromSub($subQuery->getQuery(), 't')->count();
        return round(($userRanking / $totalRanking) * 100, 2);
    }

    public function getReportByTotal(): int
    {
        return QuestionHistory::query()
            ->where('user_id', user('app')->getId())
            ->count();
    }

    public function getReportByMonth(): Collection|array
    {
        return QuestionHistory::query()
            ->selectRaw("date_format(from_unixtime(created_at), '%m') month")
            ->selectRaw('count(*) num')
            ->where('user_id', user('app')->getId())
            ->whereRaw('created_at > unix_timestamp(DATE_SUB(CURDATE(), INTERVAL 1 YEAR))')
            ->groupBy([Db::raw("date_format(from_unixtime(created_at), '%m')")])
            ->get();
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }
        // 用户输入的答案
        if (isset($params['user_answer']) && $params['user_answer'] !== '') {
            $query->where('user_answer', '=', $params['user_answer']);
        }

        // 0错误；1正确；
        if (isset($params['is_right']) && $params['is_right'] !== '') {
            $query->where('is_right', '=', $params['is_right']);
        }

        if (isset($params['is_mark']) && $params['is_mark'] !== '') {
            $query->where('is_mark', '=', $params['is_mark']);
        }

        // 收藏错题本1收藏,0不收藏
        if (isset($params['is_collect']) && $params['is_collect'] !== '') {
            $query->where('is_collect', '=', $params['is_collect']);
        }

        if (isset($params['created_at'][0], $params['created_at'][1])) {
            $query->whereBetween(
                'created_at',
                [strtotime($params['created_at'][0] . ' 00:00:00'), strtotime($params['created_at'][1] . ' 23:59:59')]
            );
        }

        if (! empty($params['withQuestion'])) {
            $query->with(['question' => function ($query) {
                $query->with(['questionSubject:value,label', 'questionType:value,label', 'knows:id,name,season']);
            }]);
        }
        return $query;
    }
}
