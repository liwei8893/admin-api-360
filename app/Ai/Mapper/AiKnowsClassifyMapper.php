<?php
declare(strict_types=1);

namespace App\Ai\Mapper;

use App\Ai\Model\AiKnowsClassify;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractMapper;

/**
 * 知识点分类Mapper类
 */
class AiKnowsClassifyMapper extends AbstractMapper
{
    /**
     * @var AiKnowsClassify
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = AiKnowsClassify::class;
    }

    /**
     * 获取前端选择树
     * @return array
     */
    public function getSelectTree(): array
    {
        return $this->model::query()
            ->select(['id', 'parent_id', 'id AS value', 'name AS label'])
            ->where('status', 1)
            ->orderByDesc('sort')
            ->get()->toTree();
    }

    /**
     * 获取App前端选择树
     * @return array
     */
    public function getAppTree(): array
    {
        return $this->model::query()
            ->select(['id', 'parent_id', 'level', 'name', 'grade', 'subject', 'ratio', 'difficulty'])
            ->where('status', 1)
            ->orderByDesc('sort')
            ->get()->toTree();
    }


    /**
     * 查询树名称
     * @param array|null $ids
     * @return array
     */
    public function getTreeName(array $ids = null): array
    {
        return $this->model::withTrashed()->whereIn('id', $ids)->pluck('name')->toArray();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function checkChildrenExists(int $id): bool
    {
        return $this->model::withTrashed()->where('parent_id', $id)->exists();
    }

    /**
     * 查找所有子元素
     * @param int $id
     * @param array $select
     * @return Collection|array
     */
    public function findChildren(int $id, array $select = ['*']): Collection|array
    {
        /* @var AiKnowsClassify $curModel */
        $curModel = $this->read($id);
        return $this->model::query()
            ->select($select)
            ->where('status', 1)
            ->where('level', 'like', "{$curModel->level},{$curModel->id},%")
            ->orWhere('level', 'like', "%,{$curModel->id}")->get();
    }

    public function updateChildren(int $id, array $data): bool
    {
        /* @var AiKnowsClassify $curModel */
        $curModel = $this->read($id);
        return (bool)$this->model::query()
            ->where('level', 'like', "{$curModel->level},{$curModel->id},%")
            ->orWhere('level', 'like', "%,{$curModel->id}")
            ->update($data);
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
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

        // 年级
        if (isset($params['grade']) && $params['grade'] !== '') {
            $query->where('grade', '=', $params['grade']);
        }

        // 科目
        if (isset($params['subject']) && $params['subject'] !== '') {
            $query->where('subject', '=', $params['subject']);
        }

        // 考试占比
        if (isset($params['ratio']) && $params['ratio'] !== '') {
            $query->where('ratio', '=', $params['ratio']);
        }

        // 难度:1易,2中,3难
        if (isset($params['difficulty']) && $params['difficulty'] !== '') {
            $query->where('difficulty', '=', $params['difficulty']);
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
