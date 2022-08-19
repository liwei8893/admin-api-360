<?php

declare (strict_types=1);
namespace App\Order\Model;

use Mine\MineModel;
/**
 * @property int $id 
 * @property int $order_id 
 * @property string $indate_start 
 * @property string $indate_end 
 * @property \Carbon\Carbon $created_at 
 * @property int $created_id 
 * @property int $status 
 * @property string $money 
 * @property string $created_name 
 * @property int $shop_id 
 * @property int $user_id 
 * @property int $audit_status 
 * @property string $remark 
 * @property string $cause_text 
 * @property int $renew_experience 续费时属性
 */
class UsersRenew extends MineModel
{
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_renew';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['order_id', 'indate_start', 'indate_end', 'created_at', 'created_id', 'status', 'money', 'created_name', 'shop_id', 'user_id', 'audit_status', 'remark', 'cause_text', 'renew_experience'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'order_id' => 'integer', 'created_at' => 'datetime', 'created_id' => 'integer', 'status' => 'string', 'money' => 'decimal:2', 'shop_id' => 'integer', 'user_id' => 'integer', 'audit_status' => 'integer', 'renew_experience' => 'integer'];

}