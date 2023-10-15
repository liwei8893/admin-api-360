<?php

declare(strict_types=1);

namespace App\Sta\Mapper;

use App\Sta\Model\DailyStatistic;
use Carbon\Carbon;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

class DailyMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = DailyStatistic::class;
    }

    public function updateOrInsertDailySta(array $params): bool
    {
        return (bool) DailyStatistic::updateOrInsert(['date' => Carbon::now()->toDateString()], $params);
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['start_date'], $params['end_date'])) {
            $query->whereBetween('date', [$params['start_date'], $params['end_date']]);
        }
        return $query;
    }
}
