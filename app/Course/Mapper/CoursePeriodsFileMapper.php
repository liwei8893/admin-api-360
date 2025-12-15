<?php
declare(strict_types=1);


namespace App\Course\Mapper;

use App\Course\Model\CoursePeriodsFile;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 章节文件Mapper类
 */
class CoursePeriodsFileMapper extends AbstractMapper
{
    /**
     * @var CoursePeriodsFile
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CoursePeriodsFile::class;
    }


//    checkFilePeriods
    public function checkFilePeriods(int $fileId, int $periodsId): bool
    {
        return $this->model::query()->where('periods_id', $periodsId)->where('file_id', $fileId)->exists();
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 关联文件表
        $query->with(['file']);
        // 章节ID
        if (isset($params['periods_id']) && $params['periods_id'] !== '') {
            $query->where('periods_id', '=', $params['periods_id']);
        }

        // 文件ID
        if (isset($params['file_id']) && $params['file_id'] !== '') {
            $query->where('file_id', '=', $params['file_id']);
        }

        // 文件名称
        if (isset($params['file_name']) && $params['file_name'] !== '') {
            $query->where('file_name', '=', $params['file_name']);
        }

        // 排序
        if (isset($params['sort']) && $params['sort'] !== '') {
            $query->where('sort', '=', $params['sort']);
        }

        return $query;
    }
}
