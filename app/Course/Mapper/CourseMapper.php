<?php

declare(strict_types=1);

namespace App\Course\Mapper;

use App\Course\Model\CourseBasis;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractMapper;

class CourseMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = CourseBasis::class;
    }

    /**
     * id数组查询课程信息.
     * @param array $select
     *                      author:ZQ
     *                      time:2021-08-31 14:49
     * @return array|Builder[]|Collection
     */
    public function getCourseInfoByIds(array $ids, array $select = []): Collection|array
    {
        if (empty($ids)) {
            return [];
        }
        return $this->model::query()->whereIn('id', $ids)
            ->when(! empty($select), static function ($query) use ($select) {
                $query->select($select);
            })
            ->get();
    }
}
