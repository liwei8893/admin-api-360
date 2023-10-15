<?php

declare(strict_types=1);

namespace App\Sta\Model;

use Mine\MineModel;

/**
 * @property int $id
 * @property \Carbon\Carbon $date 日期
 * @property int $hits pc网站点击量
 * @property int $add_user 新增用户
 * @property int $total_user 用户总数
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
    protected array $fillable = ['id', 'date', 'hits', 'add_user', 'total_user'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'date' => 'datetime:Y-m-d', 'hits' => 'integer', 'add_user' => 'integer', 'total_user' => 'integer'];

    protected array $dates = ['date'];
}
