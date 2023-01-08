<?php

declare(strict_types=1);

namespace App\System\Model;

use Carbon\Carbon;
use Mine\MineModel;

/**
 * @property int $id 区域主键
 * @property int $parent_id 上级主键
 * @property string $area_name 区域名称
 * @property string $area_code 区域代码
 * @property string $area_short 区域简称
 * @property string $area_is_hot 是否热门(0:否、1:是)
 * @property int $area_sequence 区域序列
 * @property Carbon $created_at 初始时间
 * @property string $init_addr 初始地址
 */
class Area extends MineModel
{
    public bool $timestamps = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'area';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'parent_id', 'area_name', 'area_code', 'area_short', 'area_is_hot', 'area_sequence', 'created_at', 'init_addr'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'area_sequence' => 'integer', 'created_at' => 'datetime'];
}
