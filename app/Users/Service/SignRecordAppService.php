<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\Users\Mapper\SignRecordAppMapper;
use App\Users\Mapper\UserScoreMapper;
use App\Users\Mapper\UsersMapper;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;

class SignRecordAppService extends AbstractService
{
    /**
     * @var SignRecordAppMapper
     */
    #[Inject]
    public $mapper;

    #[Inject]
    protected UsersMapper $UsersMapper;

    #[Inject]
    protected UserScoreMapper $userScoreMapper;

    public function hasSign(): bool
    {
        $userId = user()->getId();
        $curDate = date('Y-m-d');
        return $this->mapper->hasSignRecord($userId, $curDate);
    }

    /**
     * 签到.
     */
    #[Transaction]
    public function signing(): array
    {
        // 查询用户信息
        $userModel = $this->UsersMapper->read(user()->getId());
        if (! $userModel) {
            throw new NormalStatusException('用户不存在!');
        }
        $userId = $userModel['id'];
        $userDays = $userModel['days'];
        $userScore = $userModel['score'];
        $curDate = date('Y-m-d');
        // 判断是否签到
        if ($this->mapper->hasSignRecord($userId, $curDate)) {
            throw new NormalStatusException('已签到,不能重复签到!');
        }
        // 获取用户上次签到时间
        $lastSignDate = $this->mapper->lastSignDate($userId);
        if (! $lastSignDate) {
            // 不存在 表示第一次签到
            $userDays = 1;
            ++$userScore;
            $status = '首次签到,目前积分' . $userScore;
        } else {
            $lastSignDay = $lastSignDate['sign_date'];
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            // 判断是不是续签
            if ($lastSignDay === $yesterday) {
                ++$userDays;
            } else {
                // 断签
                $userDays = 1;
            }
            ++$userScore;
            $status = '连续签到' . $userDays . '天，目前积分' . $userScore;
        }
        // 保存签到数据
        $signId = $this->save(['user_id' => $userId, 'sign_date' => $curDate]);
        if (! $signId) {
            throw new NormalStatusException('签到失败!');
        }
        // 保存用户签到天数,积分
        $states = $this->userScoreMapper->updateUserDaysAndScore($userModel, $userScore, $userDays);
        if (! $states) {
            throw new NormalStatusException('签到失败!');
        }
        // 保存积分详情
        $this->userScoreMapper->saveInUserScore([
            'user_id' => $userId,
            'origin_id' => $signId,
            'channel' => '签到',
            'channel_type' => 1,
            'score' => 1,
        ]);

        return ['status' => $status, 'day' => $userDays, 'score' => $userScore];
    }
}
