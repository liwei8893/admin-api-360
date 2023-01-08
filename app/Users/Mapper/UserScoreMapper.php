<?php

declare(strict_types=1);

namespace App\Users\Mapper;

use App\Users\Model\User;
use App\Users\Model\UserScore;
use Carbon\Carbon;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractMapper;

class UserScoreMapper extends AbstractMapper
{
    /**
     * @var UserScore
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = UserScore::class;
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['user_id'])) {
            $query->where('user_id', $params['user_id']);
        }
        if (isset($params['channel_type'])) {
            $query->where('channel_type', $params['channel_type']);
        }
        if (isset($params['start_date'], $params['end_date'])) {
            $query->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
        }
        return parent::handleSearch($query, $params);
    }

    /**
     * 添加积分.
     * @param $data ['origin_id','channel','channel_type','score']
     */
    public function saveInUserScore(array $data): Model|Builder
    {
        $createData = [
            'user_id' => $data['user_id'],
            'type' => 1,
            'origin_id' => $data['origin_id'] ?? '',
            'channel' => $data['channel'] ?? '签到',
            'channel_type' => $data['channel_type'] ?? 1,
            'score' => $data['score'] ?? 1,
        ];
        return UserScore::query()->create($createData);
    }

    /**
     * 使用积分.
     * @param $data ['origin_id','channel','score']
     */
    public function saveUnUserScore(array $data): Model|Builder
    {
        $createData = [
            'user_id' => $data['user_id'],
            'type' => 0,
            'origin_id' => $data['origin_id'] ?? '',
            'channel' => $data['channel'],
            'channel_type' => $data['channel_type'] ?? 2,
            'score' => $data['score'],
        ];
        return UserScore::query()->create($createData);
    }

    /**
     * 保存用户签到天数,积分.
     */
    public function updateUserDaysAndScore(mixed $userModel, int $score, int $days): bool
    {
        $userModel->days = $days;
        $userModel->score = $score;
        return $userModel->save();
    }

    /**
     * 自增用户积分.
     */
    public function incrementUserScore(int $userId, int $score = 1): int
    {
        return User::query()->where('id', $userId)->increment('score', $score);
    }

    /**
     * 自减用户积分.
     */
    public function decrementUserScore(int $userId, int $score): int
    {
        return User::query()->where('id', $userId)->decrement('score', $score);
    }

    /**
     * 获取当天积分获得次数.
     */
    public function getCurScoreCount(int $userId, int $type): int
    {
        $startTime = Carbon::today()->timestamp;
        $endTime = Carbon::tomorrow()->timestamp;
        return UserScore::query()
            ->where('user_id', $userId)
            ->where('channel_type', $type)
            ->whereBetween('created_at', [$startTime, $endTime])
            ->count();
    }
}
