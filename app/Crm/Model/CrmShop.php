<?php

declare(strict_types=1);

namespace App\Crm\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 商品 ID，自增主键
 * @property string $shop_name 商品名称
 * @property int $category_id 商品分类 ID，关联商品分类表
 * @property string $price 商品价格
 * @property string $description 商品描述
 * @property int $status 商品状态，1 表示正常，0 表示下架
 * @property \Carbon\Carbon $created_at 商品创建时间
 * @property \Carbon\Carbon $updated_at 商品信息更新时间
 * @property string $deleted_at 商品删除时间
 * @property-read null|\Hyperf\Database\Model\Collection|CrmShopCourse[] $course 课程表
 */
class CrmShop extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'crm_shop';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'shop_name', 'category_id', 'price', 'description', 'course', 'status', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'category_id' => 'integer', 'price' => 'decimal:2', 'course' => 'array', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    //  关联商品课程表
    public function course(): HasMany
    {
        return $this->hasMany(CrmShopCourse::class, 'shop_id', 'id');
    }
}
