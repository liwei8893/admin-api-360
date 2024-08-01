<?php

declare(strict_types=1);

namespace App\Question\Model;

use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property int $user_id 用户ID
 * @property int $exam_id 试题ID
 * @property string $user_answer 用户输入的答案
 * @property int $is_right 0错误；1正确
 * @property int $is_collect 收藏错题本1收藏,0不收藏
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property-read Exam|null $exam
 */
class ExamHistory extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'exam_history';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'exam_id', 'user_answer', 'is_right', 'is_collect', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'exam_id' => 'integer', 'is_right' => 'integer', 'is_collect' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function exam(): HasOne
    {
        return $this->hasOne(Exam::class, 'id', 'exam_id');
    }

    /**
     * 定义 users 关联.
     */
    public function users(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
