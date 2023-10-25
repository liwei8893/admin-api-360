<?php

declare(strict_types=1);

namespace App\Operation\Mapper;

use App\Operation\Model\LotteryPrize;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 抽奖奖品Mapper类.
 */
class LotteryPrizeMapper extends AbstractMapper
{
    /**
     * @var LotteryPrize
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = LotteryPrize::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['lottery_id']) && $params['lottery_id'] !== '') {
            $query->where('lottery_id', $params['lottery_id']);
        }
        return $query;
    }
}
