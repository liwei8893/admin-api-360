<?php

declare(strict_types=1);

namespace App\System\Model;

use App\Course\Model\CoursePeriod;
use App\Question\Model\Question;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\MorphToMany;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id
 * @property string $name 标签名称
 * @property int $status 标签状态 0:禁用 1:正常
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class Tag extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'status' => 'string', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function coursePeriod(): MorphToMany
    {
        return $this->morphedByMany(CoursePeriod::class, 'taggable');
    }

    public function question(): MorphToMany
    {
        return $this->morphedByMany(Question::class, 'taggable');

    }
}
