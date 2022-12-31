<?php

declare(strict_types=1);

namespace App\Course\Mapper;

use App\Course\Model\CoursePeriod;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractMapper;

class CoursePeriodMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = CoursePeriod::class;
    }

    public function getPlanMonth($params): Collection|array
    {
        return CoursePeriod::query()
            ->with(['tags:id,name'])
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
}
