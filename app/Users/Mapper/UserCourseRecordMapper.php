<?php

declare(strict_types=1);

namespace App\Users\Mapper;

use App\Users\Model\UserCourseRecord;
use App\Users\Model\UserCourseRecordToday;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\DbConnection\Model\Model;
use Mine\Abstracts\AbstractMapper;

/**
 * 听课记录Mapper类.
 */
class UserCourseRecordMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = UserCourseRecord::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (! empty($params['user_id'])) {
            $query->where('user_id', $params['user_id']);
        }
        if (! empty($params['withCourseBasis'])) {
            $query->with(['courseBasis:course_basis.id,course_basis.id as course_basis_id,course_basis.title']);
        }
        if (! empty($params['withCoursePeriod'])) {
            $query->with(['coursePeriod:id,course_basis_id,title,subject_name,subject_id']);
        }
        return $query;
    }

    /**
     * 获取最后一次观看课程记录.
     */
    public function lastRecord(int $userId): Model|Builder|null
    {
        return UserCourseRecord::query()->where('user_id', $userId)->latest('updated_at')->first();
    }

    public function getRanking(): Collection|array
    {
        return UserCourseRecordToday::query(true)
            ->with(['users:id,user_name,mobile'])
            ->select(['user_id'])
            ->selectRaw('sum(record_time) as num')
            ->groupBy(['user_id'])
            ->orderBy('num', 'desc')
            ->limit(10)->get();
    }

    public function getRankingMe(): int
    {
        $userId = user('app')->getId();
        $userNum = UserCourseRecordToday::query()->where('user_id', $userId)->groupBy(['user_id'])->sum('record_time');
        $subQuery = UserCourseRecordToday::query()->selectRaw('sum(record_time) num')->groupBy(['user_id']);
        return Model::query()->fromSub($subQuery->getQuery(), 'a')->where('num', '>=', $userNum)->count();
    }
}
