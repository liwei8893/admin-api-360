<?php

declare(strict_types=1);

namespace App\Question\Model;

use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Mine\MineModel;

/**
 * @property int $id 主键ID;此表用于记录所有人做题的记录，统计分析用
 * @property int $user_id 用户ID
 * @property int $ques_id 试题ID
 * @property string $user_answer 用户输入的答案
 * @property int $is_right 0错误；1正确；
 * @property int $is_mark
 * @property int $is_collect 收藏错题本1收藏,0不收藏
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at
 * @property Question $question
 * @property User $users
 */
class QuestionHistory extends MineModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'question_history';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'ques_id', 'user_answer', 'is_right', 'is_mark', 'is_collect', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'ques_id' => 'integer', 'is_right' => 'integer', 'is_mark' => 'integer', 'is_collect' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    protected ?string $dateFormat = 'U';

    public function question(): HasOne
    {
        return $this->hasOne(Question::class, 'id', 'ques_id')
            ->where('deleted_at', 0);
    }

    /**
     * 定义 users 关联.
     */
    public function users(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
