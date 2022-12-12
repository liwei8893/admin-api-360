<?php

declare(strict_types=1);

namespace App\Question\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasMany;
use Mine\MineModel;

/**
 * @property int $id 知识点ID
 * @property int $parent_id 父级ID
 * @property int $knows_grade 知识点等级
 * @property string $knows_rule 知识点规则
 * @property string $name 知识点名称
 * @property int $states 状态:0:显示 1:隐藏
 * @property int $sort 排序
 * @property int $season 全科班季节分类用 1春,2夏,3秋,4寒
 * @property int $status 0 禁用 1正常
 * @property int $deleted_at 删除时间
 * @property int $created_id 创建人
 * @property Carbon $created_at 创建时间
 * @property int $updated_id 修改人
 * @property Carbon $updated_at 修改时间
 * @property int $grade_id
 * @property string $shop_id
 */
class Know extends MineModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'knows';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'parent_id', 'knows_grade', 'knows_rule', 'name', 'states', 'sort', 'season', 'status', 'deleted_at', 'created_id', 'created_at', 'updated_id', 'updated_at', 'grade_id', 'shop_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'parent_id' => 'integer', 'knows_grade' => 'integer', 'states' => 'integer', 'sort' => 'integer', 'season' => 'string', 'status' => 'string', 'deleted_at' => 'integer', 'created_id' => 'integer', 'created_at' => 'datetime', 'updated_id' => 'integer', 'updated_at' => 'datetime', 'grade_id' => 'string'];

    public function question(): HasMany
    {
        return $this->hasMany(Question::class, 'knows_id', 'id');
    }
}
