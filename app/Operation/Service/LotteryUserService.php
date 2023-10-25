<?php

declare(strict_types=1);

namespace App\Operation\Service;

use App\Operation\Mapper\LotteryUserMapper;
use App\Operation\Model\Lottery;
use Exception;
use Hyperf\Collection\Collection;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;

/**
 * 抽奖名单服务类.
 */
class LotteryUserService extends AbstractService
{
    /**
     * @var LotteryUserMapper
     */
    public $mapper;

    #[Inject]
    public LotteryService $lotteryService;

    public function __construct(LotteryUserMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 检测抽奖资格
     */
    public function hasPermission(int $id): bool
    {
        $userId = user('app')->getId();
        /* @var Lottery $lotteryModel */
        $lotteryModel = $this->lotteryService->read($id);
        if (! $lotteryModel) {
            throw new NormalStatusException('抽奖活动不存在');
        }
        // 查询是否已经抽过奖
        $hasLotteryUser = $lotteryModel->lotteryUser()->where('user_id', $userId)->exists();
        if ($hasLotteryUser) {
            return false;
        }
        // 查询用户订单,是否在活动时间内
        $params = [
            'userId' => $userId,
            'startTime' => $lotteryModel->start_time->timestamp,
            'endTime' => $lotteryModel->end_time->timestamp,
        ];
        return $this->mapper->hasUserOrder($params);
    }

    /**
     * 抽奖,保存抽奖人员信息.
     */
    public function saveLotteryUser(int $id): array
    {
        $userId = user('app')->getId();
        // TODO 上线时打开资格验证代码块
        //        $hasPermission = $this->hasPermission($id);
        //        if (! $hasPermission) {
        //            throw new NormalStatusException('暂无抽奖资格');
        //        }
        /* @var Lottery $lotteryModel */
        $lotteryModel = $this->lotteryService->read($id);
        if (! $lotteryModel) {
            throw new NormalStatusException('抽奖活动不存在');
        }
        // 拿到抽奖活动的奖品列表和概率,查询有库存的奖品
        $lotteryPrize = $lotteryModel->lotteryPrize()->where('last_num', '>', 0)->get();
        // 计算获奖奖品
        $prizeId = $this->handleLotteryRate($lotteryPrize);
        $prizeModel = $lotteryPrize->where('id', $prizeId)->first();
        if (! $prizeModel) {
            $name = '谢谢参与';
        } else {
            $name = $prizeModel->name;
        }
        // 写数据库
        $this->mapper->saveLotteryUser(['lottery_prize_id' => $prizeId, 'lottery_id' => $id, 'user_id' => $userId]);
        return ['id' => $prizeId, 'name' => $name];
    }

    /**
     * 返回中奖商品ID.
     * @param mixed $lotteryPrize
     */
    protected function handleLotteryRate(Collection $lotteryPrize): int
    {
        // 抽奖
        try {
            $num = random_int(1, 100);
            foreach ($lotteryPrize->shuffle() as $item) {
                $rate = $item->rate;
                if ($num <= $rate) {
                    return $item->id;
                }
                $num -= $rate;
            }
        } catch (Exception $e) {
            throw new NormalStatusException('抽奖失败,请重新抽奖');
        }
        return 0;
    }
}
