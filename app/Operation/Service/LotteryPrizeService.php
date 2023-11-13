<?php

declare(strict_types=1);

namespace App\Operation\Service;

use App\Operation\Mapper\LotteryPrizeMapper;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

/**
 * 抽奖奖品服务类.
 */
class LotteryPrizeService extends AbstractService
{
    /**
     * @var LotteryPrizeMapper
     */
    public $mapper;

    #[Inject]
    protected LotteryService $lotteryService;

    public function __construct(LotteryPrizeMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        $data['last_num'] = $data['num'];
        $data['created_by'] = user()->getId();
        $data['updated_by'] = user()->getId();
        return parent::save($data);
    }

    /**
     * 获取列表数据.
     */
    public function getList(?array $params = null, bool $isScope = true): array
    {
        // 没有指定抽奖ID,默认获取当天抽奖ID
        if (! isset($params['lottery_id'])) {
            $params['lottery_id'] = $this->lotteryService->getNowDateId();
            if (! $params['lottery_id']) {
                return [];
            }
        }
        $params['select'] = 'id,name';
        return parent::getList($params, $isScope);
    }

    /**
     * 更新一条数据.
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_by'] = user()->getId();
        // 不能编辑奖品剩余数量,只能在中奖时候更新
        if (isset($data['last_num'])) {
            unset($data['last_num']);
        }
        return parent::update($id, $data);
    }
}
