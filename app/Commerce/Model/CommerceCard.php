<?php

declare(strict_types=1);

namespace App\Commerce\Model;

use App\Course\Model\CourseBasis;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id ID
 * @property int $card_id 卡号
 * @property int $course_id 课程ID
 * @property int $status 是否使用
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property null|CourseBasis $course
 * @property null|CommerceCardUsage $usage
 */
class CommerceCard extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'commerce_card';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'card_id', 'course_id', 'status', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'card_id' => 'integer', 'course_id' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function course(): HasOne
    {
        return $this->hasOne(CourseBasis::class, 'id', 'course_id');
    }

    public function usage(): HasOne
    {
        return $this->hasOne(CommerceCardUsage::class, 'card_id', 'card_id');
    }
}
