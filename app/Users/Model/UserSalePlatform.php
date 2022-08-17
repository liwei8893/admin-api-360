<?php

namespace App\Users\Model;

use Mine\MineModel;
/**
 * @property int $id 
 * @property string $user_platform 平台
 * @property int $u_sale_platform 
 */
class UserSalePlatform extends MineModel
{
    protected $table = 'user_sale_platform';
    public $timestamps = false;
    protected $fillable = ['user_platform', 'u_sale_platform'];

}