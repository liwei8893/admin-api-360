<?php

declare(strict_types=1);

namespace App\Course\Model;

use App\System\Model\SystemDictData;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property string $title 课程名称
 * @property int $price 金额
 * @property int $day 天数
 * @property int $real_year 真实报名年数
 * @property int $sort 排序
 * @property string $remark 备注
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property CourseBasis[]|\Hyperf\Database\Model\Collection $courseSignup
 * @property \Hyperf\Database\Model\Collection|SystemDictData[] $gradeSignup
 */
class CourseSignupConfig extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'course_signup_config';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'price', 'day', 'real_year', 'sort', 'remark', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'price' => 'integer', 'day' => 'integer', 'real_year' => 'integer', 'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function courseSignup(): BelongsToMany
    {
        return $this->belongsToMany(CourseBasis::class, 'course_signup', 'course_signup_config_id', 'course_id');
    }

    public function gradeSignup(): BelongsToMany
    {
        return $this->belongsToMany(SystemDictData::class, 'course_signup_grade', 'course_signup_config_id', 'grade_id', 'id', 'value')
            ->select(['id', 'label as title', 'value as key'])
            ->where('code', 'grade')->where('status', MineModel::ENABLE);
    }
}
