<?php

declare(strict_types=1);

namespace App\Course\Mapper;

use App\Course\Model\CoursePeriod;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\HasOne;
use Mine\Abstracts\AbstractMapper;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;

class CoursePeriodMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = CoursePeriod::class;
    }

    /**
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    public function getPlanMonth(array $params): Collection|array
    {
        $isLogin = user('app')->hasLogin();
        $userId = $isLogin ? user('app')->getId() : null;
        return CoursePeriod::query()
            ->with(['tags:id,name', 'courseRecord' => function (HasOne $query) use ($userId) {
                $query->where('user_id', $userId)
                    ->select(['id', 'period_id', 'video_duration', 'watch_time']);
            }])
            ->select(['id', 'title', 'subject_id', 'subject_name', 'course_basis_id'])
            ->whereHas('courseBasis', function (Builder $query) use ($params) {
                $query->whereHas('basisGrade', function (Builder $query) use ($params) {
                    $query->where('grade_id', $params['grade_id']);
                })
                    ->where('states', 3)
                    ->where('is_del', 0)
                    ->where('course_title', 30)
                    ->when(isset($params['subject_id']), function (Builder $query) use ($params) {
                        $query->where('subject_id', $params['subject_id']);
                    })
                    ->when(isset($params['season']), function (Builder $query) use ($params) {
                        $query->where('season', $params['season']);
                    });
            })->limit($params['limit'])->offset($params['offset'])->get();
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['tagId']) && ! is_array($params['tagId'])) {
            $query->whereHas('tags', function (Builder $query) use ($params) {
                $query->where('id', $params['tagId']);
            });
        }
        if (isset($params['tagId']) && is_array($params['tagId'])) {
            $query->whereHas('tags', function (Builder $query) use ($params) {
                $query->whereIn('id', $params['tagId']);
            });
        }
        if (isset($params['courseStatus'])) {
            $query->whereHas('courseBasis', function (Builder $query) use ($params) {
                $query->where('states', $params['courseStatus']);
            });
        }
        return $query;
    }
}
