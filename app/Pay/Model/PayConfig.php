<?php

declare(strict_types=1);

namespace App\Pay\Model;

use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 
 * @property string $remark 备注
 * @property string $appid APP APPID
 * @property string $app_id 公众号 APPID
 * @property string $miniapp_id 小程序 APPID
 * @property int $mch_id 商户号
 * @property string $key 商户秘钥
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property int $deleted_at 删除时间
 */
class PayConfig extends MineModel
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'pay_config';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'remark', 'appid', 'app_id', 'miniapp_id', 'mch_id', 'key', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'mch_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];
}
