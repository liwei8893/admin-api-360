<?php

declare(strict_types=1);

namespace App\Crm\Model;

use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Mine\MineModel;

/**
 * @property int $id 主键ID
 * @property int $user_id 用户ID
 * @property string $comm_time 沟通时间
 * @property string $content 沟通内容摘要
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 */
class CrmUserCommTimeline extends MineModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'crm_user_comm_timeline';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'comm_time', 'content', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
