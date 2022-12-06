<?php

declare(strict_types=1);

namespace App\System\Model;

use Carbon\Carbon;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $user_id 用户ID
 * @property string $content 短信内容
 * @property string $mobile 短信手机号
 * @property string $return_code 返回状态码
 * @property string $sms_code 短信验证码
 * @property string $sms_func 发送短信的方法
 * @property string $sms_ip 发送的IP
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 */
class SmsLog extends MineModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sms_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'content', 'mobile', 'return_code', 'sms_code', 'sms_func', 'sms_ip', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
