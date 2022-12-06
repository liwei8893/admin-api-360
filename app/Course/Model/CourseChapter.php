<?php

declare(strict_types=1);

namespace App\Course\Model;

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
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_chapter';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'course_basis_id', 'title', 'serial_num', 'parent_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'course_basis_id' => 'integer', 'serial_num' => 'integer', 'parent_id' => 'integer'];
}
