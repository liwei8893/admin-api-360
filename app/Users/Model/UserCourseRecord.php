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
 * @property string $time_rate 完课率
 * @property int $complete_status 完成状态:0未完成,1已经完成,2已经消费
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $deleted_at
 * @property-read User|null $users
 * @property-read CoursePeriod|null $coursePeriod
 * @property-read CourseBasis|null $courseBasis
 */
class UserCourseRecord extends MineModel
{
    use SoftDeletes;

    // 多长时间算完成
    public const COMPLETE_TIME_RATE = 50;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_course_record';

    protected ?string $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'period_id', 'user_id', 'video_duration', 'watch_time', 'time_rate', 'complete_status', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'period_id' => 'integer', 'user_id' => 'integer', 'video_duration' => 'integer', 'watch_time' => 'integer', 'time_rate' => 'decimal:2', 'complete_status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];

    protected array $appends = ['watch_time|video_duration' => 'timeRate'];

    /**
     * 完课率
     * author:ZQ
     * time:2022-08-28 16:32.
     */
    public function getTimeRateAttribute(): float
    {
        if (isset($this->attributes['watch_time'], $this->attributes['video_duration']) && $this->attributes['video_duration'] * 100 !== 0) {
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
