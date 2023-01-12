<?php

declare(strict_types=1);

namespace App\Operation\Mapper;

use App\Operation\Model\InformationType;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 资讯分类Mapper类.
 */
class InformationTypeMapper extends AbstractMapper
{
    /**
     * @var InformationType
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = InformationType::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        // 资讯分类名称
        if (isset($params['name']) && $params['name'] !== '') {
            $query->where('name', '=', $params['name']);
        }

        // 分类介绍
        if (isset($params['type_info']) && $params['type_info'] !== '') {
            $query->where('type_info', '=', $params['type_info']);
        }

        return $query;
    }
}
