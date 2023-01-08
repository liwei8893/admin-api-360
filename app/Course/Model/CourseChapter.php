<?php

declare(strict_types=1);

namespace App\Course\Model;

use Hyperf\Database\Model\Relations\HasOne;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $course_basis_id 课程基本信息ID
 * @property string $title 章的名称
 * @property int $serial_num 序号
 * @property int $parent_id 父ID
 */
class CourseChapter extends MineModel
{
    public bool $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'course_chapter';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'course_basis_id', 'title', 'serial_num', 'parent_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'course_basis_id' => 'integer', 'serial_num' => 'integer', 'parent_id' => 'integer'];

    /**
     * 关联章节表.
     */
    public function coursePeriod(): HasOne
    {
        return $this->hasOne(CoursePeriod::class, 'course_chapter_id', 'id');
    }

    /**
     * 关联课程表.
     */
    public function courseBasis(): HasOne
    {
        return $this->hasOne(CourseBasis::class, 'id', 'course_basis_id');
    }
}
