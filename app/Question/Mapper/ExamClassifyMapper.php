<?php

declare(strict_types=1);

namespace App\Question\Mapper;

use App\Question\Model\ExamClassify;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 试卷分类Mapper类.
 */
class ExamClassifyMapper extends AbstractMapper
{
    /**
     * @var ExamClassify
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = ExamClassify::class;
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
        // 父ID
        if (isset($params['parent_id']) && $params['parent_id'] !== '') {
            $query->where('parent_id', '=', $params['parent_id']);
        }

        // 组级集合
        if (isset($params['level']) && $params['level'] !== '') {
            $query->where('level', 'like', '%' . $params['level'] . '%');
        }

        // 菜单名称
        if (isset($params['name']) && $params['name'] !== '') {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        // 状态 (1正常 0停用)
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', '=', $params['status']);
        }

        // 排序
        if (isset($params['sort']) && $params['sort'] !== '') {
            $query->where('sort', '=', $params['sort']);
        }

        return $query;
    }
}
