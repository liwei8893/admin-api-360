<?php

declare(strict_types=1);

namespace App\Users\Model;

use App\Course\Model\CourseBasis;
use App\Course\Model\CoursePeriod;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\Relations\HasOneThrough;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $period_id 章节id
 * @property int $user_id 用户id
 * @property int $video_duration 视频总时长
 * @property int $watch_time 观看时长
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $deleted_at
 * @property CourseBasis $courseBasis
 * @property CoursePeriod $coursePeriod
 * @property float $time_rate
 * @property User $users
 */
class UserCourseRecord extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_course_record';

    protected $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'period_id', 'user_id', 'video_duration', 'watch_time', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'period_id' => 'integer', 'user_id' => 'integer', 'video_duration' => 'integer', 'watch_time' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];

    protected $appends = ['watch_time|video_duration' => 'timeRate'];

    /**
     * 完课率
     * author:ZQ
     * time:2022-08-28 16:32.
     */
    public function getTimeRateAttribute(): float
    {
        if (isset($this->attributes['watch_time'], $this->attributes['video_duration'])) {
            return round($this->attributes['watch_time'] / $this->attributes['video_duration'] * 100, 2);
        }
        return 0.0;
    }

    /**
     * 定义 users 关联.
     */
    public function users(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * 定义 coursePeriod 关联.
     */
    public function coursePeriod(): HasOne
    {
        return $this->hasOne(CoursePeriod::class, 'id', 'period_id');
    }

    /**
     * 远程关联课程表
     * author:ZQ
     * time:2022-01-10 16:39.
     */
    public function courseBasis(): hasOneThrough
    {
        return $this->hasOneThrough(CourseBasis::class, CoursePeriod::class, 'id', 'id', 'period_id', 'course_basis_id');
    }
}
