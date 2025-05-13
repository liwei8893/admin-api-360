<?php

declare(strict_types=1);

namespace App\Crm\Model;

use App\System\Model\SystemUser;
use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 订单 ID，自增主键
 * @property int $user_id 关联用户
 * @property int $shop_id 商品 ID，关联商品表
 * @property string $order_number 订单编号，唯一标识
 * @property string $amount 订单金额
 * @property int $order_status 订单状态1:未支付 2:已支付 3:已取消 4:已删除 5:退款中 6:已退款 7:已完成 8:待审核 9:审核拒绝
 * @property int $task_type 任务类型
 * @property int $address_id 地址信息 ID，关联用户地址表
 * @property string $logistics_company 物流公司
 * @property string $tracking_number 物流单号
 * @property string $order_note 订单备注
 * @property int $created_by 创建人 ID，关联用户表
 * @property \Carbon\Carbon $created_at 订单创建时间
 * @property \Carbon\Carbon $updated_at 订单信息更新时间
 * @property string $deleted_at 订单删除时间
 * @property-read null|CrmShop $shop 
 * @property-read null|CrmUserAddress $address 
 * @property-read null|User $user 
 * @property-read null|SystemUser $admin 
 */
class CrmShopOrder extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'crm_shop_order';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'shop_id', 'order_number', 'amount', 'order_status', 'task_type', 'address_id', 'logistics_company', 'tracking_number', 'order_note', 'created_by', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'shop_id' => 'integer', 'amount' => 'decimal:2', 'order_status' => 'integer', 'task_type' => 'integer', 'address_id' => 'integer', 'created_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(CrmShop::class, 'shop_id', 'id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(CrmUserAddress::class, 'address_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, 'created_by', 'id');
    }
}
