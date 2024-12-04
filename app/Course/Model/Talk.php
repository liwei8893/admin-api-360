<?php

declare(strict_types=1);

namespace App\Course\Model;

use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $user_id
 * @property int $course_period_id 关联章节表ID
 * @property int $status 默认2需要审核,通过为1,拒绝为0
 * @property string $url 视频url
 * @property string $teacher_comment 老师点评
 * @property int $created_by
 * @property int $updated_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $deleted_at
 * @property-read User|null $user
 * @property-read CoursePeriod|null $coursePeriod
 * @property-read Collection|User[]|null $userVote
 */
class Talk extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'talk';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'course_period_id', 'status', 'url', 'teacher_comment', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'course_period_id' => 'integer', 'status' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];

    protected ?string $dateFormat = 'U';

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function coursePeriod(): HasOne
    {
        return $this->hasOne(CoursePeriod::class, 'id', 'course_period_id');
    }

    public function userVote(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'talk_vote', 'talk_id', 'user_id');
    }
}
