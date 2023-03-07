<?php

declare(strict_types=1);

namespace App\System\Mapper;

use App\System\Model\Tag;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;

/**
 * 标签管理Mapper类.
 */
class TagsMapper extends AbstractMapper
{
    /**
     * @var Tag
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Tag::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['id']) && ! is_array($params['id'])) {
            $query->where('id', $params['id']);
        }
        if (isset($params['id']) && is_array($params['id'])) {
            $query->whereIn('id', $params['id']);
        }
        // 标签名称
        if (isset($params['name']) && $params['name'] !== '') {
            $query->where('name', $params['name']);
        }

        // 标签状态 0:禁用 1:正常
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', '=', $params['status']);
        }

        return $query;
    }

    /**
     * 单个或批量真实删除数据.
     */
    #[Transaction]
    public function realDelete(array $ids): bool
    {
        foreach ($ids as $id) {
            $model = $this->model::withTrashed()->find($id);
            if ($model) {
                $model->coursePeriod()->detach();
                $model->question()->detach();
                $model->forceDelete();
            }
        }
        return true;
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        $model = $this->model::firstOrCreate(['name' => $data['name']], $data);
        return $model->{$model->getKeyName()};
    }
}
