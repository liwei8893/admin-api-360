<?php

declare(strict_types=1);

namespace App\Sta\Mapper;

use App\Sta\Model\DailyStatistic;
use Carbon\Carbon;
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
}
