<?php

declare(strict_types=1);

namespace App\System\Mapper;

use App\System\Model\Tag;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

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
}
