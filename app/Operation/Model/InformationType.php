<?php

declare(strict_types=1);

namespace App\Operation\Model;

use Carbon\Carbon;
use Mine\MineModel;

/**
 * @property int $id
 * @property string $name 资讯分类名称
 * @property string $type_info 分类介绍
 * @property int $parent_id 父id
 * @property int $created_id 创建人
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 修改时间
 */
class InformationType extends MineModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'information_type';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'type_info', 'parent_id', 'created_id', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'created_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    protected ?string $dateFormat = 'U';
}
