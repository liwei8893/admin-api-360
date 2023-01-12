<?php

declare(strict_types=1);

namespace App\Operation\Mapper;

use App\Operation\Model\Banner;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 轮播管理Mapper类.
 */
class BannerMapper extends AbstractMapper
{
    /**
     * @var Banner
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Banner::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // banner端： 1 pc  2 h5  3 小程序  4 app 5图书
        if (isset($params['banner_type_id']) && $params['banner_type_id'] !== '') {
            $query->where('banner_type_id', '=', $params['banner_type_id']);
        }

        // 图片路径
        if (isset($params['banner_img']) && $params['banner_img'] !== '') {
            $query->where('banner_img', '=', $params['banner_img']);
        }

        // 排序
        if (isset($params['sorc']) && $params['sorc'] !== '') {
            $query->where('sorc', '=', $params['sorc']);
        }

        // 标题
        if (isset($params['title']) && $params['title'] !== '') {
            $query->where('title', '=', $params['title']);
        }

        // 是否启用的状态(0启用，1不启用)
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', '=', $params['status']);
        }

        return $query;
    }
}
