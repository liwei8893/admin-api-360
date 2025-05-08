<?php

declare(strict_types=1);

namespace App\Crm\Model;

use App\System\Model\SystemUser;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Mine\MineModel;

/**
 * @property int $id ID
 * @property int $user_id 用户ID
 * @property int $created_by 创建人id
 * @property string $event 事件
 * @property string $event_detail 事件详情
 * @property Carbon $created_at 创建时间
 */
class CrmUserTimeline extends MineModel
{
    public bool $timestamps = false;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'crm_user_timeline';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'created_by', 'event', 'event_detail', 'created_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'created_by' => 'integer', 'created_at' => 'datetime'];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, 'created_by', 'id');
    }
}
