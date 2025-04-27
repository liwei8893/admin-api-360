<?php

declare(strict_types=1);

namespace App\Pay\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property string $remark 备注
 * @property string $app_id APP APPID
 * @property string $mp_app_id 公众号 APPID
 * @property string $mini_app_id 小程序 APPID
 * @property string $mch_id 商户号
 * @property string $mch_secret_key 商户秘钥 API v3 密钥
 * @property string $mch_secret_cert 商户私钥API证书 PRIVATE KEY
 * @property string $mch_public_cert_path 商户公钥证书路径API证书 CERTIFICATE
 * @property string $key v2商户秘钥
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
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
    protected array $fillable = ['id', 'remark', 'app_id', 'mp_app_id', 'mini_app_id', 'mch_id', 'mch_secret_key', 'mch_secret_cert', 'mch_public_cert_path', 'key', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
