<?php

declare(strict_types=1);

namespace App\Course\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 
 * @property string $title 标题
 * @property string $title_desc 副标题
 * @property int $title_rule 0:标题,副标题全部显示,1:只显示标题,2:只显示副标题
 * @property int $show_rule 0:购买之后才显示,1:总是显示
 * @property int $vip_auth 2超级会员,3至尊会员
 * @property string $image_pc pc端图片链接
 * @property string $image_h5 h5端图片链接
 * @property string $detail 详情
 * @property int $sort 排序大到小
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property-read \Hyperf\Database\Model\Collection|CourseBasis[] $courseBasis 
 */
class CourseShop extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'course_shop';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'title_desc', 'title_rule', 'show_rule', 'vip_auth', 'image_pc', 'image_h5', 'detail', 'sort', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'title_rule' => 'integer', 'show_rule' => 'integer', 'vip_auth' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function courseBasis(): BelongsToMany
    {
        return $this->belongsToMany(CourseBasis::class, 'course_shop_id', 'shop_id', 'course_id', 'id', 'id');
    }
}
