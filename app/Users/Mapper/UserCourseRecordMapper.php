<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Users\Mapper;

use App\Users\Model\UserCourseRecord;
use Hyperf\Database\Model\Builder;
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
}
