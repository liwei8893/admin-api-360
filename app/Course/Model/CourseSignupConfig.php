<?php

declare (strict_types=1);

namespace App\Course\Model;

use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property string $title 课程名称
 * @property int $price 金额
 * @property int $day 天数
 * @property string $remark 备注
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 */
class CourseSignupConfig extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_signup_config';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'title', 'price', 'day', 'sort', 'remark', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'price' => 'integer', 'day' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function CourseSignup(): \Hyperf\Database\Model\Relations\BelongsToMany
    {
        return $this->belongsToMany(CourseBasis::class, 'course_signup', 'course_signup_config_id', 'course_id');
    }
}
