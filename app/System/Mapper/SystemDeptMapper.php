<?php

declare(strict_types=1);

namespace App\System\Mapper;

use App\System\Model\SystemDept;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;
use Mine\MineCollection;

class SystemDeptMapper extends AbstractMapper
{
    /**
     * @var SystemDept
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = SystemDept::class;
    }

    /**
     * 获取前端选择树.
     */
    public function getSelectTree(): array
    {
        $treeData = $this->model::query()->select(['id', 'parent_id', 'id AS value', 'name AS label'])
            ->where('status', $this->model::ENABLE)
            ->orderBy('sort', 'desc')
            ->userDataScope()
            ->platformDataScope()
            ->get()->toArray();
        return (new MineCollection())->toTree($treeData, $treeData[0]['parent_id'] ?? 0);
    }

    /**
     * 查询部门名称.
     */
    public function getDeptName(array $ids = null): array
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
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (isset($params['platform'])) {
            $query->where('platform', $params['platform']);
        }

        if (isset($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['leader'])) {
            $query->where('leader', $params['leader']);
        }

        if (isset($params['phone'])) {
            $query->where('phone', $params['phone']);
        }

        if (isset($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0] . ' 00:00:00', $params['created_at'][1] . ' 23:59:59']
            );
        }
        return $query;
    }

    /**
     * 获取部门平台下拉.
     * @return mixed
     *               author:ZQ
     *               time:2022-05-29 15:42
     */
    public function getPlatformSelect(): mixed
    {
        return $this->model::where('status', $this->model::ENABLE)
            ->whereNotNull('platform')
            ->select(['id', 'name as title', 'platform as key'])
            ->orderBy('sort', 'desc')
            ->platformDataScope()
            ->get();
    }
}
