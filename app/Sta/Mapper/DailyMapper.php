<?php

declare(strict_types=1);

namespace App\Sta\Mapper;

use App\Sta\Model\DailyStatistic;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractMapper;

class DailyMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = DailyStatistic::class;
    }

    public function setDailyHits(array $params): bool
    {
        $model = DailyStatistic::firstOrCreate(['date' => Carbon::now()->toDateString()]);
        if (isset($params['type']) && $params['type'] === 'h5') {
            ++$model->h5_hits;
        } else {
            ++$model->hits;
        }
        return $model->save();
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
