<?php

declare(strict_types=1);

namespace App\Pay\Model;

use App\Course\Model\CourseBasis;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property string $remark 备注
 * @property string $platform 平台编号
 * @property int $config_id pay_config表ID
 * @property int $auth_id pay_auth表ID
 * @property int $price 价格
 * @property string $image 图片
 * @property array $view_config 样式配置
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property int $deleted_at 删除时间
 * @property PayAuth $payAuth
 * @property PayConfig $payConfig
 * @property Collection|CourseBasis[] $payCourse
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
    protected array $fillable = ['id', 'remark', 'platform', 'config_id', 'auth_id', 'price', 'image', 'view_config', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'config_id' => 'integer', 'auth_id' => 'integer', 'price' => 'integer', 'view_config' => 'array', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];

    protected ?string $dateFormat = 'U';

    public function payAuth(): HasOne
    {
        return $this->hasOne(PayAuth::class, 'id', 'auth_id');
    }

    public function payConfig(): HasOne
    {
        return $this->hasOne(PayConfig::class, 'id', 'config_id');
    }

    public function payCourse(): BelongsToMany
    {
        return $this->belongsToMany(CourseBasis::class, 'pay_link_course', 'link_id', 'course_id');
    }
}
