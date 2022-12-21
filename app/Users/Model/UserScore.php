<?php

declare(strict_types=1);

namespace App\Users\Model;

use Carbon\Carbon;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $user_id 用户id
 * @property int $type 类型 1增加 0减少
 * @property string $channel 渠道
 * @property int $channel_type 1签到,2会员积分,3认证积分,4听课积分,5做题积分,6分享积分,7兑换头像,8兑换头像框,9兑换课程
 * @property int $origin_id 来源ID
 * @property int $score 积分数量
 * @property Carbon $created_at 时间
 * @property Carbon $updated_at
 */
class UserScore extends MineModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_score';

    protected $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'type', 'channel', 'channel_type', 'origin_id', 'score', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'type' => 'integer', 'channel_type' => 'integer', 'origin_id' => 'integer', 'score' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
