<?php

declare(strict_types=1);

namespace App\Sta\Model;

use Carbon\Carbon;
use Mine\MineModel;

/**
 * @property int $id
 * @property Carbon $time 时间
 * @property string $browser 浏览器
 * @property string $client_ip IP
 * @property string $region 地区
 * @property string $page 访问页面
 * @property int $device 设备1:pc,2:h5
 */
class StaAccessLog extends MineModel
{
    public bool $timestamps = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'sta_access_log';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'time', 'browser', 'client_ip', 'region', 'page', 'device'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'time' => 'datetime', 'device' => 'integer'];

    protected array $dates = ['time'];

    protected ?string $dateFormat = 'U';
}
