<?php

declare(strict_types=1);

namespace App\Operation\Mapper;

use App\Operation\Model\LotteryUser;
use App\Order\Model\Order;
use App\Users\Model\User;
use Exception;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractMapper;
use Mine\Exception\NormalStatusException;

/**
 * 抽奖名单Mapper类.
 */
class LotteryUserMapper extends AbstractMapper
{
    /**
     * @var LotteryUser
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = LotteryUser::class;
    }

    /**
     * 判断是否在活动时间内购买课程.
     * @param $params array
     */
    public function hasUserOrder(array $params): bool
    {
        return Order::query()
            ->where('user_id', $params['userId'])
            ->normalOrder()
            ->isNotExpire()
            ->whereIn('shop_id', [User::VIP_TYPE_SUPER, ...User::VIP_TYPE_HIGH])
            ->where('created_at', '>=', $params['startTime'])
            ->where('created_at', '<=', $params['endTime'])
            ->exists();
    }

    public function saveLotteryUser(array $params): bool
    {
        try {
            DB::beginTransaction();
            // 添加中奖记录
            $lotteryModel = LotteryUser::query()->create($params);
            // 更新奖品库存
            $prizeId = $params['lottery_prize_id'];
            $lotteryModel->lotteryPrize()->where('id', $prizeId)->decrement('last_num');
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new NormalStatusException('抽奖失败,请重试!');
        }
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }

        // 奖品ID
        if (isset($params['lottery_prize_id']) && $params['lottery_prize_id'] !== '') {
            $query->where('lottery_prize_id', '=', $params['lottery_prize_id']);
        }

        // 抽奖ID
        if (isset($params['lottery_id']) && $params['lottery_id'] !== '') {
            $query->where('lottery_id', '=', $params['lottery_id']);
        }

        if (isset($params['withUser']) && $params['withUser']) {
            $query->with('user:id,user_name,mobile');
        }
        if (isset($params['withLotteryPrize']) && $params['withLotteryPrize']) {
            $query->with('lotteryPrize:id,name');
        }
        if (isset($params['mobile']) && $params['mobile'] !== '') {
            $query->whereHas('user', function ($query) use ($params) {
                $query->where('mobile', 'like', $params['mobile'] . '%');
            });
        }
        return $query;
    }
}
