<?php

declare(strict_types=1);

namespace App\Score\Mapper;

use App\Score\Model\ScoreShop;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 积分管理Mapper类.
 */
class ScoreShopMapper extends AbstractMapper
{
    /**
     * @var ScoreShop
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = ScoreShop::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 1:头像,3课程
        if (isset($params['shop_type']) && $params['shop_type'] !== '') {
            $query->where('shop_type', '=', $params['shop_type']);
        }

        // type1:avatar,type2:avatar,type3:course_basis
        if (isset($params['shop_id']) && $params['shop_id'] !== '') {
            $query->where('shop_id', '=', $params['shop_id']);
        }

        // 兑换需要的积分数
        if (isset($params['score']) && $params['score'] !== '') {
            $query->where('score', '=', $params['score']);
        }
        if (! empty($params['withShop'])) {
            $query->with('shop');
        }
        return $query;
    }
}
