<?php

declare(strict_types=1);

namespace App\Operation\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $classify_id 资讯分类id
 * @property string $information_title 资讯标题
 * @property string $picture 资讯图片
 * @property string $source 资讯来源
 * @property string $abstract 摘要
 * @property string $content 资讯内容
 * @property int $created_id 创建人
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property int $status 状态：1已发布 2未发布
 * @property int $click_rate 点击率
 */
class Information extends MineModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'information';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'classify_id', 'information_title', 'picture', 'source', 'abstract', 'content', 'created_id', 'created_at', 'updated_at', 'status', 'click_rate'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'classify_id' => 'integer', 'created_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'status' => 'integer', 'click_rate' => 'integer'];

    protected ?string $dateFormat = 'U';

    public function informationType(): HasOne
    {
        return $this->hasOne(InformationType::class, 'id', 'classify_id');
    }
}
