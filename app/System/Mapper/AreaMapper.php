<?php

declare(strict_types=1);

namespace App\System\Mapper;

use App\System\Model\Area;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractMapper;

/**
 * 区域字典Mapper类.
 */
class AreaMapper extends AbstractMapper
{
    /**
     * @var Area
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Area::class;
    }

    public function getAreaByAreaName($areaName): Area|Model|Builder|null
    {
        return $this->model::query()->where('area_name', 'like', "%{$areaName}%")
            ->first();
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 区域名称
        if (isset($params['area_name']) && $params['area_name'] !== '') {
            $query->where('area_name', '=', $params['area_name']);
        }

        // 区域代码
        if (isset($params['parent_id']) && $params['parent_id'] !== '') {
            $query->where('parent_id', '=', $params['parent_id']);
        }

        if (isset($params['area_code']) && $params['area_code'] !== '') {
            $query->where('area_code', '=', $params['area_code']);
        }

        // 区域简称
        if (isset($params['area_short']) && $params['area_short'] !== '') {
            $query->where('area_short', '=', $params['area_short']);
        }

        // 是否热门(0:否、1:是)
        if (isset($params['area_is_hot']) && $params['area_is_hot'] !== '') {
            $query->where('area_is_hot', '=', $params['area_is_hot']);
        }

        // 区域序列
        if (isset($params['area_sequence']) && $params['area_sequence'] !== '') {
            $query->where('area_sequence', '=', $params['area_sequence']);
        }

        // 初始地址
        if (isset($params['init_addr']) && $params['init_addr'] !== '') {
            $query->where('init_addr', '=', $params['init_addr']);
        }

        return $query;
    }
}
