<?php

declare(strict_types=1);

namespace App\Users\Model;

use Carbon\Carbon;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $user_id 用户ID
 * @property string $sign_date 签到时间
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SignRecord extends MineModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'sign_record';

    protected ?string $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'sign_date', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
