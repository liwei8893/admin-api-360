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
        if (!empty($params['user_id'])) {
            $query->where('user_id', $params['user_id']);
        }
        if (!empty($params['withCourseBasis'])) {
            $query->with(['courseBasis:course_basis.id,course_basis.id as course_basis_id,course_basis.title']);
        }
        if (!empty($params['withCoursePeriod'])) {
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

    public function getRanking(array $params = []): Collection|array
    {
        $params['start_date'] = $params['start_date'] ?? Carbon::now()->startOfMonth();
        $params['end_date'] = $params['end_date'] ?? Carbon::now()->endOfMonth();
        return UserCourseRecordToday::query(true)
            ->with(['users:id,user_name,mobile'])
            ->select(['user_id'])
            ->selectRaw('sum(record_time) as num')
            ->whereBetween('record_date', [$params['start_date'], $params['end_date']])
            ->groupBy(['user_id'])
            ->orderBy('num', 'desc')
            ->limit(10)->get();
    }

    public function getRankingMe(int $userId, array $params = []): int
    {
        $params['start_date'] = $params['start_date'] ?? Carbon::now()->startOfMonth();
        $params['end_date'] = $params['end_date'] ?? Carbon::now()->endOfMonth();
        $userNum = UserCourseRecordToday::query()
            ->where('user_id', $userId)->groupBy(['user_id'])
            ->whereBetween('record_date', [$params['start_date'], $params['end_date']])
            ->sum('record_time');
        $subQuery = UserCourseRecordToday::query()
            ->selectRaw('sum(record_time) num')
            ->whereBetween('record_date', [$params['start_date'], $params['end_date']])
            ->groupBy(['user_id']);
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
        ])->where('user_id', $userId)->orderBy('updated_at', 'desc')->get();
    }

    public function getRecordPageList(array $params): array
    {
        $query = UserCourseRecord::with([
            'courseBasis:course_basis.id,course_basis.id as course_basis_id,course_basis.title',
            'coursePeriod:id,course_basis_id,title',
            'users:id,user_name,mobile',
        ])->where('user_id', $params['userId'])->orderBy('updated_at', 'desc');
        $perPage = $params['pageSize'] ?? $this->model::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $query = $query->paginate(
            (int)$perPage,
            ['*'],
            'page',
            (int)$page
        );
        return $this->setPaginate($query);
    }

    /**
     * 完课率
     */
    public function getTimeRate(int $videoDuration, int $watchTime): float
    {
        if (isset($watchTime, $videoDuration) && $videoDuration * 100 !== 0) {
            return round($watchTime / $videoDuration * 100, 2);
        }
        return 0.00;
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
        // 计算听课率
        $recordModel->time_rate = $this->getTimeRate((int)$params['videoDuration'], $recordModel->watch_time);
        // 计算是否完成
        $recordModel->complete_status = $recordModel->time_rate >= UserCourseRecord::COMPLETE_TIME_RATE ? 1 : 0;
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
     * @param mixed $userId
     */
    public function getTodayDataByUserId($userId): null|Builder|\Hyperf\Database\Model\Model|UserCourseRecordToday
    {
        return UserCourseRecordToday::query()->where('user_id', $userId)
            ->where('record_date', Carbon::today()->toDateString())->first();
    }

    /**
     * 获取未使用的番茄数量
     * @param int $userId
     * @return int
     */
    public function getUnusedTomatoCount(int $userId): int
    {
        return UserCourseRecord::query()->where('user_id', $userId)->where('complete_status', 1)->count();
    }

    /**
     * 获取一个未使用的番茄
     * @param int $userId
     * @return UserCourseRecord|\Hyperf\Database\Model\Model|Builder|null
     */
    public function getUnusedTomatoFirst(int $userId): UserCourseRecord|\Hyperf\Database\Model\Model|Builder|null
    {
        return UserCourseRecord::query()->where('user_id', $userId)->where('complete_status', 1)->first();
    }
}
