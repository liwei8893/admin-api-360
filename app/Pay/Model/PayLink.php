<?php

declare(strict_types=1);

namespace App\Pay\Model;

use App\Course\Model\CourseBasis;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 
 * @property string $remark 备注
 * @property string $platform 平台编号
 * @property int $config_id pay_config表ID
 * @property int $auth_id pay_auth表ID
 * @property int $course_id 课程ID
 * @property int $price 价格
 * @property int $indate 有效期(天)
 * @property string $image 图片
 * @property array $view_config 样式配置
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property int $deleted_at 删除时间
 * @property-read PayAuth $payAuth 
 * @property-read PayConfig $payConfig 
 * @property-read CourseBasis $course 
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
    protected array $fillable = ['id', 'remark', 'platform', 'config_id', 'auth_id', 'course_id', 'price', 'indate', 'image', 'view_config', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'config_id' => 'integer', 'auth_id' => 'integer', 'course_id' => 'integer', 'price' => 'integer', 'indate' => 'integer', 'view_config' => 'array', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];

    protected ?string $dateFormat = 'U';

    public function payAuth(): HasOne
    {
        return $this->hasOne(PayAuth::class, 'id', 'auth_id');
    }

    public function payConfig(): HasOne
    {
        return $this->hasOne(PayConfig::class, 'id', 'config_id');
    }

    public function course(): HasOne
    {
        return $this->hasOne(CourseBasis::class, 'id', 'course_id');
    }
}
