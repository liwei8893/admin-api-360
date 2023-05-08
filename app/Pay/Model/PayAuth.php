<?php

declare(strict_types=1);

namespace App\Pay\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property string $remark 备注
 * @property string $appid 公众号appid
 * @property string $app_secret 公众号秘钥
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property int $deleted_at 删除时间
 */
class PayAuth extends MineModel
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'pay_auth';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'remark', 'appid', 'app_secret', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];

    protected ?string $dateFormat = 'U';
}
