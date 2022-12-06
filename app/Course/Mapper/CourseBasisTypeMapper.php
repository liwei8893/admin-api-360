<?php

declare(strict_types=1);

namespace App\Course\Mapper;

use App\Course\Model\CourseBasisType;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 课程分类Mapper类.
 */
class CourseBasisTypeMapper extends AbstractMapper
{
    /**
     * @var CourseBasisType
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CourseBasisType::class;
    }

    /**
     * 获取前端选择树.
     */
    public function getSelectTree(): array
    {
        return $this->model::query()
            ->select(['id', 'parent_id', 'id AS value', 'name AS label'])
            ->get()->toTree();
    }

    /**
     * 查询树名称.
     */
    public function getTreeName(array $ids = null): array
    {
        return $this->model::withTrashed()->whereIn('id', $ids)->pluck('name')->toArray();
    }

    public function checkChildrenExists(int $id): bool
    {
        return $this->model::withTrashed()->where('parent_id', $id)->exists();
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['name']) && $params['name'] !== '') {
            $query->where('name', '=', $params['name']);
        }

        if (isset($params['parent_id']) && $params['parent_id'] !== '') {
            $query->where('parent_id', '=', $params['parent_id']);
        }

        if (isset($params['level']) && $params['level'] !== '') {
            $query->where('level', '=', $params['level']);
        }

        if (isset($params['states']) && $params['states'] !== '') {
            $query->where('states', '=', $params['states']);
        }

        if (isset($params['title_id']) && $params['title_id'] !== '') {
            $query->where('title_id', '=', $params['title_id']);
        }

        return $query;
    }
}
