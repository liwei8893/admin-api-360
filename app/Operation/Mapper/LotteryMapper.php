<?php

declare(strict_types=1);

namespace App\Operation\Mapper;

use App\Operation\Model\Lottery;
use Carbon\Carbon;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 抽奖管理Mapper类.
 */
class LotteryMapper extends AbstractMapper
{
    /**
     * @var Lottery
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Lottery::class;
    }

    public function getNowDateId(): null|int
    {
        return $this->model::query()->where('start_time', '>=', Carbon::now()->startOfDay()->timestamp)->where('end_time', '<=', Carbon::now()->addDay()->startOfDay()->timestamp)->value('id');
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 抽奖活动名称
        if (isset($params['name']) && $params['name'] !== '') {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        // 抽奖活动开始时间
        if (isset($params['start_time']) && $params['start_time'] !== '') {
            $query->where('start_time', '>=', $params['start_time']);
        }

        // 抽奖活动结束时间
        if (isset($params['end_time']) && $params['end_time'] !== '') {
            $query->where('end_time', '<=', $params['end_time']);
        }

        return $query;
    }
}
