<?php

declare(strict_types=1);

namespace App\Users\Model;

use App\System\Model\SystemUser;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id ID
 * @property int $user_id 关联用户ID
 * @property int $type 备注类型,1常规,2售后
 * @property int $after_sale_type 售后类型,1承诺一对一,2直播课,3找不到课程老师,4无理由退费
 * @property string $remark 备注
 * @property int $has_completed 售后是否完成
 * @property int $created_by 创建人ID
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property-read SystemUser|null $adminUser
 * @property-read User|null $user
 */
class UserRemark extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_remark';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'type', 'after_sale_type', 'remark', 'has_completed', 'created_by', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'type' => 'integer', 'after_sale_type' => 'integer', 'has_completed' => 'integer', 'created_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    
    public function adminUser(): hasOne
    {
        return $this->hasOne(SystemUser::class, 'id', 'created_by');
    }

    /**
     * 定义 user 关联.
     */
    public function user(): hasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
