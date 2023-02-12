<?php

declare(strict_types=1);

namespace App\Users\Mapper;

use App\Users\Model\UserCourseRecord;
use App\Users\Model\UserCourseRecordToday;
use Carbon\Carbon;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\DbConnection\Db;
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

    public function getRankingMe(int $userId): int
    {
        $userNum = UserCourseRecordToday::query()->where('user_id', $userId)->groupBy(['user_id'])->sum('record_time');
        $subQuery = UserCourseRecordToday::query()->selectRaw('sum(record_time) num')->groupBy(['user_id']);
        return Model::query()->fromSub($subQuery->getQuery(), 'a')->where('num', '>=', $userNum)->count();
    }

    public function getRankingRate(int $userId): float|int
    {
        $userRanking = $this->getRankingMe($userId);
        $subQuery = UserCourseRecord::query()->selectRaw('count(user_id) num')->groupBy(['user_id']);
        $totalRanking = Model::query()->fromSub($subQuery->getQuery(), 't')->count();
        return round(($userRanking / $totalRanking) * 100, 2);
    }

    public function getReportByTotal(int $userId): int
    {
        return UserCourseRecord::query()
            ->where('user_id', $userId)
            ->count();
    }

    public function getReportByMonth(): Collection|array
    {
        return UserCourseRecord::query()
            ->selectRaw("date_format(from_unixtime(created_at), '%m') month")
            ->selectRaw('count(*) num')
            ->where('user_id', user('app')->getId())
            ->whereRaw('created_at > unix_timestamp(DATE_SUB(CURDATE(), INTERVAL 1 YEAR))')
            ->groupBy([Db::raw("date_format(from_unixtime(created_at), '%m')")])
            ->get();
    }

    public function getRecordByUserId($userId): Collection|array
    {
        return UserCourseRecord::with([
            'courseBasis:course_basis.id,course_basis.id as course_basis_id,course_basis.title',
            'coursePeriod:id,course_basis_id,title',
            'users:id,user_name,mobile',
        ])->where('user_id', $userId)->get();
    }

    /**
     * 设置课程观看时间.
     */
    public function setWatchTime(array $params): bool
    {
        $recordModel = UserCourseRecord::query()
            ->firstOrNew(
                ['user_id' => $params['userId'], 'period_id' => $params['periodId']],
                ['video_duration' => $params['videoDuration']]
            );
        $recordModel->watch_time += $params['watchTime'];
        return $recordModel->save();
    }

    /**
     * 设置课程每天观看时间.
     */
    public function setWatchTimeToday(array $params): bool
    {
        $today = Carbon::today()->toDateString();
        $recordModel = UserCourseRecordToday::query()
            ->firstOrNew(['user_id' => $params['userId'], 'record_date' => $today]);
        $recordModel->record_time += $params['watchTime'];
        return $recordModel->save();
    }

    /**
     * 查询用户当天听课时长
     */
    public function getTodayDataByUserId($userId): \Hyperf\Database\Model\Model|UserCourseRecordToday|Builder|null
    {
        return UserCourseRecordToday::query()->where('user_id', $userId)
            ->where('record_date', Carbon::today()->toDateString())->first();
    }
}
