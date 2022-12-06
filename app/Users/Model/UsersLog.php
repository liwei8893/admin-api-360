<?php

declare(strict_types=1);

namespace App\Users\Model;

use Mine\MineModel;

/**
 * @property int $id
 * @property int $users_id 用户ID
 * @property string $last_login_ip 最后登录IP
 * @property int $last_login_time 最后登录时间
 * @property int $continuous_count 连续登录天数
 * @property string $api_token 接口Token
 * @property string $device_id 设备ID
 * @property int $device_type 0:苹果,1:H5,2:PC,3:微信,4:小程序
 */
class UsersLog extends MineModel
{
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'users_id', 'last_login_ip', 'last_login_time', 'continuous_count', 'api_token', 'device_id', 'device_type'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'users_id' => 'integer', 'last_login_time' => 'integer', 'continuous_count' => 'integer', 'device_type' => 'integer'];
}
