<?php

declare(strict_types=1);

namespace App\Order\Model;

use Mine\MineModel;

/**
 * @property int $id
 * @property int $order_id 订单ID
 * @property int $admin_id 管理员ID
 */
class OrderSummaryAdmin extends MineModel
{
    public bool $timestamps = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'order_summary_admin';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'order_id', 'admin_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'order_id' => 'integer', 'admin_id' => 'integer'];
}
