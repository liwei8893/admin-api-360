<?php

declare(strict_types=1);

namespace App\Pay\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property int $link_id 链接id
 * @property int $course_id 课程id
 * @property string $course_title 课程名称
 * @property string $price 金额
 * @property int $day 报名天数
 * @property string $real_year 真实报名年数
 * @property int $chapter_count_auth 章节权限0不限制,其他数量表示可以观看前面多少节课
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class PayLinkCourse extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'pay_link_course';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'link_id', 'course_id', 'course_title', 'price', 'day', 'real_year', 'chapter_count_auth', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'link_id' => 'integer', 'course_id' => 'integer', 'price' => 'decimal:2', 'day' => 'integer', 'real_year' => 'decimal:2', 'chapter_count_auth' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
