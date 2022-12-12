<?php

declare(strict_types=1);

namespace App\Question\Mapper;

use App\Question\Model\Know;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 题库管理Mapper类.
 */
class KnowsMapper extends AbstractMapper
{
    /**
     * @var Know
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Know::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 知识点名称
        if (isset($params['name']) && $params['name'] !== '') {
            $query->where('name', '=', $params['name']);
        }

        // 排序
        if (isset($params['sort']) && $params['sort'] !== '') {
            $query->where('sort', '=', $params['sort']);
        }

        // 全科班季节分类用 1春,2夏,3秋,4寒
        if (isset($params['season']) && $params['season'] !== '') {
            $query->where('season', '=', $params['season']);
        }

        // 0 禁用 1正常
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', '=', $params['status']);
        }

        if (isset($params['grade_id']) && $params['grade_id'] !== '') {
            $query->where('grade_id', '=', $params['grade_id']);
        }

        if (isset($params['shop_id']) && $params['shop_id'] !== '') {
            $query->where('shop_id', '=', $params['shop_id']);
        }

        $query->where('deleted_at', 0);
        return $query;
    }
}
