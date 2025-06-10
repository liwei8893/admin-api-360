<?php

declare(strict_types=1);

namespace App\Course\Model;

use App\System\Model\SystemUploadfile;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Mine\MineModel;

/**
 * @property int $id ID
 * @property int $periods_id 章节ID
 * @property int $file_id 文件ID
 * @property string $file_name 文件名称
 * @property int $sort 排序
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 */
class CoursePeriodsFile extends MineModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'course_periods_file';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'periods_id', 'file_id', 'file_name', 'sort', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'periods_id' => 'integer', 'file_id' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function file(): HasOne
    {
        return $this->hasOne(SystemUploadfile::class, 'id', 'file_id');
    }
}
