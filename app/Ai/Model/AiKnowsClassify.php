<?php

declare(strict_types=1);

namespace App\Ai\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property int $parent_id 父ID
 * @property string $level 组级集合
 * @property string $name 菜单名称
 * @property int $grade 年级
 * @property int $subject 科目
 * @property string $ratio 考试占比
 * @property int $status 状态 (1正常 0停用)
 * @property int $sort 排序
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class AiKnowsClassify extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'ai_knows_classify';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'parent_id', 'level', 'name', 'grade', 'subject', 'ratio', 'status', 'sort', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'grade' => 'integer', 'subject' => 'integer', 'ratio' => 'decimal:2', 'status' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
