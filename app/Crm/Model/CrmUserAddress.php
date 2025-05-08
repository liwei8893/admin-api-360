<?php

declare(strict_types=1);

namespace App\Crm\Model;

use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 地址记录 ID，自增主键
 * @property int $user_id 用户 ID，关联用户表
 * @property string $consignee 收货人姓名
 * @property string $phone 收货人联系电话
 * @property string $province 省份
 * @property string $city 城市
 * @property string $area 区县
 * @property string $detail_address 详细地址
 * @property string $postal_code 邮政编码
 * @property int $is_default 是否为默认地址，0 表示非默认，1 表示默认
 * @property \Carbon\Carbon $created_at 地址创建时间
 * @property \Carbon\Carbon $updated_at 地址更新时间
 * @property string $deleted_at 删除时间
 */
class CrmUserAddress extends MineModel
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'crm_user_address';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'consignee', 'phone', 'province', 'city', 'area', 'detail_address', 'postal_code', 'is_default', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'is_default' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
