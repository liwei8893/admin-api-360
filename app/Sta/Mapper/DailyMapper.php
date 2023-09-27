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

    public function setDailyHits(): bool
    {
        $model = DailyStatistic::firstOrCreate(['date' => Carbon::now()->toDateString()]);
        ++$model->hits;
        return $model->save();
    }
}
