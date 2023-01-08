<?php

declare(strict_types=1);

namespace App\Course\Model;

use Mine\MineModel;

/**
 * @property int $id
 * @property string $course_name
 * @property string $sub_title 副标题
 * @property int $sort 排序
 * @property string $img_url
 * @property string $video_url
 * @property string $type_name
 * @property int $type
 * @property string $nav_to 跳转
 * @property string $pc_img_url pc图片地址
 * @property int $grade 年级
 * @property int $subject 1语文,2数学,3英语,4物理,5化学,6生物,7地理,8政治,9历史,10文综,11理综
 */
class CourseIndex extends MineModel
{
    public bool $timestamps = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'course_index';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'course_name', 'sub_title', 'sort', 'img_url', 'video_url', 'type_name', 'type', 'nav_to', 'pc_img_url', 'grade', 'subject'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'sort' => 'integer', 'type' => 'integer', 'grade' => 'integer', 'subject' => 'integer'];
}
