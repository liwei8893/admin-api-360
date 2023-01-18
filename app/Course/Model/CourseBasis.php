<?php

declare(strict_types=1);

namespace App\Course\Model;

use App\Order\Model\Order;
use App\Score\Model\ScoreShop;
use App\System\Model\SystemDictData;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\Relations\MorphOne;
use Mine\MineModel;

/**
 * @property int $id
 * @property string $title 标题
 * @property string $subtitle 副标题
 * @property int $course_type 课程类型：1直播, 4公开课, 5录播课, 7讲座, 8音频课, 9系统课
 * @property int $course_sub_type 课程为公开课的时候选择子分类 1最强大脑，2思维导图，3作文
 * @property int $course_classify_id 课程分类
 * @property int $sort
 * @property int $origin_price 原价
 * @property int $vip_price vip_price
 * @property string $course_cover 封面图片
 * @property string $cover_video 视频封面
 * @property int $advance_time 提前进入时间
 * @property int $is_free 是否免费
 * @property int $is_playback 支持回放
 * @property int $is_generated_class 生成班级
 * @property int $is_vip_class 会员课程,1:要单独购买才能观看
 * @property int $watch_num 可观看次数
 * @property int $validity_date 视频有效期
 * @property int $start_play_date 播放开始时间
 * @property int $end_play_date 播放结束时间
 * @property int $start_play_year 播放时间年份
 * @property int $sales_num 可售数量
 * @property int $sales_base 销售基数
 * @property int $browse_base 浏览基数
 * @property int $states 状态
 * @property int $browse_num 浏览量
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property int $indate 有效期，单位天
 * @property int $need_address 是否需要地址
 * @property int $is_del 是否删除
 * @property int $is_top 是否置顶
 * @property int $is_hot 是否设置成热门 0不是 1热门
 * @property string $material_name 教材名称
 * @property string $note 课程说明
 * @property int $class_id 班级ID
 * @property int $is_group 是否分组直播,0否1是
 * @property int $grade_id 年级
 * @property int $subject_id 科目
 * @property int $is_deal 是否处理过该课程(学习报告使用)
 * @property int $is_signup 报名,0不可以1可以
 * @property int $course_title
 * @property int $course_second_title 二级分类id
 * @property string $other_img 360课程封面
 * @property int $is_playback_type 1普通 2超级 3至尊
 * @property int $is_show_pic 前端是否显示价格0不显示1显示
 * @property int $season 全科班季节分类用 1春,2夏,3秋,4寒
 * @property int $is_show_sub_title 前端是否以子标题作为标题显示,0否,1是
 * @property int $vip_type 1:优享会员,2:超级会员,3:至尊会员
 * @property int $is_give 1:表示是活动赠送的课程,如果学员购买了这个课添加到素养课里面
 * @property int $class_type 0:小学中学高中都能查,1小学,2中学,3高中
 * @property Collection|SystemDictData[] $basisGrade
 * @property CourseBasisType $basisType
 * @property Collection|CourseChapter[] $chapter
 * @property mixed $price 课程价格
 * @property Collection|Order[] $order
 * @property ScoreShop $scoreShop
 */
class CourseBasis extends MineModel
{
    public const COMMON_FIELDS = ['id', 'title', 'subtitle', 'course_cover', 'price', 'is_give', 'is_show_sub_title', 'season', 'is_show_pic', 'course_title', 'is_signup', 'subject_id'];

    public const STATUS_NORMAL = 3;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'course_basis';

    protected ?string $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'subtitle', 'course_type', 'course_sub_type', 'course_classify_id', 'sort', 'price', 'origin_price', 'vip_price', 'course_cover', 'cover_video', 'advance_time', 'is_free', 'is_playback', 'is_generated_class', 'is_vip_class', 'watch_num', 'validity_date', 'start_play_date', 'end_play_date', 'start_play_year', 'sales_num', 'sales_base', 'browse_base', 'states', 'browse_num', 'created_at', 'updated_at', 'indate', 'need_address', 'is_del', 'is_top', 'is_hot', 'material_name', 'note', 'class_id', 'is_group', 'grade_id', 'subject_id', 'is_deal', 'is_signup', 'course_title', 'course_second_title', 'other_img', 'is_playback_type', 'is_show_pic', 'season', 'is_show_sub_title', 'vip_type', 'is_give', 'class_type'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'course_type' => 'string', 'course_sub_type' => 'integer', 'course_classify_id' => 'integer', 'sort' => 'integer', 'price' => 'integer', 'origin_price' => 'integer', 'vip_price' => 'integer', 'advance_time' => 'integer', 'is_free' => 'integer', 'is_playback' => 'integer', 'is_generated_class' => 'integer', 'is_vip_class' => 'integer', 'watch_num' => 'integer', 'validity_date' => 'integer', 'start_play_date' => 'integer', 'end_play_date' => 'integer', 'start_play_year' => 'integer', 'sales_num' => 'integer', 'sales_base' => 'integer', 'browse_base' => 'integer', 'states' => 'string', 'browse_num' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'indate' => 'integer', 'need_address' => 'integer', 'is_del' => 'integer', 'is_top' => 'integer', 'is_hot' => 'integer', 'class_id' => 'integer', 'is_group' => 'integer', 'grade_id' => 'integer', 'subject_id' => 'string', 'is_deal' => 'integer', 'is_signup' => 'string', 'course_title' => 'string', 'course_second_title' => 'integer', 'is_playback_type' => 'integer', 'is_show_pic' => 'integer', 'season' => 'integer', 'is_show_sub_title' => 'integer', 'vip_type' => 'string', 'is_give' => 'integer', 'class_type' => 'integer'];

    public function getPriceAttribute(): float|int
    {
        return $this->attributes['price'] / 100;
    }

    public function setPriceAttribute($value): void
    {
        $this->attributes['price'] = (int) $value * 100;
    }

    public function basisType(): HasOne
    {
        return $this->hasOne(CourseBasisType::class, 'id', 'course_title');
    }

    public function basisGrade(): BelongsToMany
    {
        return $this->belongsToMany(SystemDictData::class, 'course_basis_grade', 'course_basis_id', 'grade_id', 'id', 'value')
            ->select(['system_dict_data.id', 'label as title', 'value as key'])
            ->where('code', 'grade')->where('status', MineModel::ENABLE);
    }

    public function chapter(): HasMany
    {
        return $this->hasMany(CourseChapter::class, 'course_basis_id', 'id');
    }

    /**
     * 多态一对一关联积分商品表.
     */
    public function scoreShop(): MorphOne
    {
        return $this->morphOne(ScoreShop::class, 'shop');
    }

    /**
     * 关联订单表.
     */
    public function order(): HasMany
    {
        return $this->hasMany(Order::class, 'shop_id', 'id');
    }
}
