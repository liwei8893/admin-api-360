<?php

declare(strict_types=1);

namespace App\Users\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $record_time 时长(秒)
 * @property string $record_date 观看日期
 * @property Carbon $created_at 详细日期
 * @property Carbon $updated_at
 * @property int $deleted_at
 */
class UserCourseRecordToday extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_course_record_today';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'record_time', 'record_date', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'record_time' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];

    /**
     * 定义 users 关联.
     */
    public function users(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
