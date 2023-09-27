<?php

declare(strict_types=1);

namespace App\Sta\Model;

use Carbon\Carbon;
use Mine\MineModel;

/**
 * @property int $id
 * @property Carbon $date 日期
 * @property int $hits 网站点击量
 */
class DailyStatistic extends MineModel
{
    public bool $timestamps = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'daily_statistics';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'date', 'hits'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'date' => 'datetime', 'hits' => 'integer'];

    protected array $dates = ['date'];
}
