<?php

declare(strict_types=1);

namespace App\Sta\Mapper;

use App\Sta\Model\DailyStatistic;
use App\Sta\Model\StaAccessLog;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractMapper;

class StaAccessLogMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = StaAccessLog::class;
    }

    public function setAccessLog(array $params): bool
    {
        return StaAccessLog::insert($params);
    }

    public function getDailyHits(): Collection|array|\Hyperf\Collection\Collection
    {
        $data = DailyStatistic::query()
            ->whereBetween('date', [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()])
            ->get();
        $dayData = $data->first('date', Carbon::now()->toDateString());
        if ($dayData) {
            $dayData = ['hits' => $dayData->hits, 'h5_hits' => $dayData->h5_hits];
        }
        $monthPc = $data->sum('hits');
        $monthH5 = $data->sum('h5_hits');
        return ['day' => $dayData, 'month' => ['hits' => $monthPc, 'h5_hits' => $monthH5]];
    }
}
