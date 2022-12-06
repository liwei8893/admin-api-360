<?php

declare(strict_types=1);

namespace App\Order\Mapper;

use App\Order\Model\UsersRenew;
use Mine\Abstracts\AbstractMapper;

class UsersRenewMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = UsersRenew::class;
    }

    /**
     * 续费表插入数据.
     * @return bool
     *              author:ZQ
     *              time:2022-08-19 14:44
     */
    public function insert(array $value): bool
    {
        return UsersRenew::query()->insert($value);
    }

    /**
     * 修改用户ID.
     * @param int $userId 用户id
     * @param int $orderId 订单id
     * @param int $newUserId 新用户id
     * @return int
     *             author:ZQ
     *             time:2022-08-21 13:09
     */
    public function transformUser(int $userId, int $orderId, int $newUserId, int $newOrderId): int
    {
        return $this->model::query()->where('user_id', $userId)
            ->where('order_id', $orderId)
            ->update(['user_id' => $newUserId, 'order_id' => $newOrderId]);
    }
}
