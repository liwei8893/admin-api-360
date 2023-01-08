<?php

declare(strict_types=1);

namespace App\Course\Model;

use App\Question\Model\Question;
use App\System\Model\Tag;
use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\Relations\MorphToMany;
use Mine\MineModel;

/**
 * @property int $id 课程基本信息 ID
 * @property string $title 课时名称
 * @property int $course_basis_id 课程基本表ID
 * @property int $course_chapter_id 课时ID
 * @property string $room_video_id 百家云视频ID或房间ID
 * @property string $room_id 房间ID
 * @property int $is_playback 是否回放
 * @property int $is_free 是否免费
 * @property int $is_vip_class 是否免费
 * @property int $is_try_see 是否试看
 * @property int $is_download 是否可以下载 0 不可以 1 可以
 * @property int $try_see_time 试看时间
 * @property int $start_play 开始播放时间
 * @property int $end_play 结束播放时间
 * @property string $start_play_date 开播年月日
 * @property string $admin_code 管理员进入房间的参加码
 * @property string $teacher_code 老师进入房间的参加码
 * @property string $student_code 学生公共参加码
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $is_push 是否推送,0否1是
 * @property int $is_getroominfo 0未拉取直播教室学员观看记录;1已成功拉取到数据；2已拉取未成功或未拉取到数据;
 * @property int $cloud_type 云平台，0 = 百家云，1 = 腾讯云
 * @property string $tencent_play_url
 * @property string $teacher_id 讲师id，多个用逗号分隔
 * @property string $assistant_id 助教id，多个用逗号分隔
 * @property string $template_name
 * @property string $play_back_url
 * @property string $assistant_name
 * @property int $is_login
 * @property string $subject_id
 * @property string $subject_name
 * @property string $qiniu_url 七牛返回url
 * @property string $filePath
 * @property int $is_group_live
 * @property string $qurstion_str
 */
class CoursePeriod extends MineModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_periods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'title', 'course_basis_id', 'course_chapter_id', 'room_video_id', 'room_id', 'is_playback', 'is_free', 'is_vip_class', 'is_try_see', 'is_download', 'try_see_time', 'start_play', 'end_play', 'start_play_date', 'admin_code', 'teacher_code', 'student_code', 'created_at', 'updated_at', 'is_push', 'is_getroominfo', 'cloud_type', 'tencent_play_url', 'teacher_id', 'assistant_id', 'template_name', 'play_back_url', 'assistant_name', 'is_login', 'subject_id', 'subject_name', 'qiniu_url', 'filePath', 'is_group_live', 'qurstion_str'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'course_basis_id' => 'integer', 'course_chapter_id' => 'integer', 'is_playback' => 'integer', 'is_free' => 'integer', 'is_vip_class' => 'integer', 'is_try_see' => 'integer', 'is_download' => 'integer', 'try_see_time' => 'integer', 'start_play' => 'integer', 'end_play' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'is_push' => 'integer', 'is_getroominfo' => 'integer', 'cloud_type' => 'integer', 'is_login' => 'integer', 'is_group_live' => 'integer'];

    protected $dateFormat = 'U';

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id', 'id')->select(['id', 'user_name', 'mobile']);
    }

    public function talk(): HasMany
    {
        return $this->hasMany(Talk::class, 'course_period_id', 'id');
    }

    public function sun(): HasMany
    {
        return $this->hasMany(Sun::class, 'course_period_id', 'id');
    }

    public function questionPeriod(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'question_period', 'period_id', 'question_id')
            ->withPivot('type');
    }

    public function courseBasis(): HasOne
    {
        return $this->hasOne(CourseBasis::class, 'id', 'course_basis_id');
    }

    /**
     * 多态关联标签.
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
