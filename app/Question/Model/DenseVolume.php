<?php

declare(strict_types=1);

namespace App\Question\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $month 视频解析月
 * @property string $name 视频解析名称
 * @property int $grade 一年级:1,二年级:2,三年级:5,四年级:7,五年级:9,六年级:11,七年级:14,八年级:13,九年级:12,高中:51
 * @property int $subject 语文:3,数学:4,英语:6,物理:15,化学:8,生物:25,地理:26,政治:23,历史:24,文综53,理综:54
 * @property int $type 1月考优题,2期中优题,3期末优题,4中考优题,5高一优题,6高二优题,7高三优题
 * @property int $answer 0试题,1答案
 * @property int $is_vip 2:超级会员能看
 * @property string $url 解析URL
 * @property int $new_state 是否最新,最新置顶
 * @property int $difficulty 难度字段 0:全部,1一星,2二星,3三星,4四星,5五星
 * @property int $sort 排序
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property int $created_by
 * @property int $updated_by
 */
class DenseVolume extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'dense_volume';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'month', 'name', 'grade', 'subject', 'type', 'answer', 'is_vip', 'url', 'new_state', 'difficulty', 'sort', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'month' => 'integer', 'grade' => 'integer', 'subject' => 'integer', 'type' => 'integer', 'answer' => 'integer', 'is_vip' => 'integer', 'new_state' => 'integer', 'difficulty' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'created_by' => 'integer', 'updated_by' => 'integer'];
}
