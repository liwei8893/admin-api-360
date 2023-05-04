<?php

declare(strict_types=1);

namespace App\Pay\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property string $remark 备注
 * @property string $platform_code 平台编号
 * @property string $platform_name 平台名称
 * @property int $config_id pay_config表ID
 * @property int $auth_id pay_auth表ID
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property int $deleted_at 删除时间
 */
class PayLink extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'pay_link';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'remark', 'platform_code', 'platform_name', 'config_id', 'auth_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'config_id' => 'integer', 'auth_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];

    public function payAuth(): HasOne
    {
        return $this->hasOne(PayAuth::class, 'id', 'auth_id');
    }

    public function payConfig(): HasOne
    {
        return $this->hasOne(PayConfig::class, 'id', 'config_id');
    }

    public function payImg(): BelongsToMany
    {
        return $this->belongsToMany(PayImg::class, 'pay_link_img', 'link_id', 'img_id');
    }
}
