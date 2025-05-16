<?php

declare(strict_types=1);

namespace App\Crm\Model;

use Mine\MineModel;

/**
 * @property int $id 自增主键
 * @property int $user_id 用户ID
 * @property string $name 姓名
 * @property string $phone 电话
 * @property string $tutor_teacher 辅导老师
 * @property string $sales_teacher 销售老师
 * @property string $main_teacher 主讲老师
 * @property string $grade 年级
 * @property string $remark 备注
 * @property string $course_name 课程名称
 * @property string $lesson_name 课次名称
 * @property string $enter_class_time 进教室时间
 * @property string $leave_class_time 离开教室时间
 * @property string $live_duration 直播时长
 * @property string $playback_duration 回放时长
 * @property int $interaction_count 互动次数
 */
class CrmStudyRecord extends MineModel
{
    public bool $timestamps = false;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'crm_study_record';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'name', 'phone', 'tutor_teacher', 'sales_teacher', 'main_teacher', 'grade', 'remark', 'course_name', 'lesson_name', 'enter_class_time', 'leave_class_time', 'live_duration', 'playback_duration', 'interaction_count'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'interaction_count' => 'integer'];
}
