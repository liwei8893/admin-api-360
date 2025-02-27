<?php

declare(strict_types=1);

namespace App\Sta\Model;

use Carbon\Carbon;
use Mine\MineModel;

/**
 * @property int $id
 * @property Carbon $date 日期
 * @property int $hits pc网站点击量
 * @property int $add_user 新增用户
 * @property int $total_user 用户总数
 * @property int $login_user 登录人数
 * @property int $learning_user 学习人数
 * @property int $ques_user 做题人数
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
    protected array $fillable = ['id', 'date', 'hits', 'add_user', 'total_user', 'login_user', 'learning_user', 'ques_user'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'date' => 'datetime', 'hits' => 'integer', 'add_user' => 'integer', 'total_user' => 'integer', 'login_user' => 'integer', 'learning_user' => 'integer', 'ques_user' => 'integer'];

    protected array $dates = ['date'];
}
