<?php

declare(strict_types=1);

namespace App\Order\Mapper;

use App\Order\Model\Order;
use Mine\Abstracts\AbstractMapper;

/**
 * 订单管理Mapper类.
 */
class OrderSignupMapper extends AbstractMapper
{
    /**
     * @var Order
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Order::class;
    }

    /**
     * 查询用户报名的正常课程.
     * @param mixed $userId
     * @param mixed $courseIds
     * @return mixed
     *               author:ZQ
     *               time:2022-08-26 15:47
     */
    public function getUserCourseInfo($userId, $courseIds): mixed
    {
        return $this->model::query()->normalOrder()
            ->whereIn('shop_id', $courseIds)
            ->where('user_id', $userId)->get();
    }

    /**
     * 获取唯一订单号.
     * @throws \Exception
     *                    author:ZQ
     *                    time:2022-08-26 16:08
     */
    public function getOrderSn(): string
    {
        $rand_num = random_int(0, 99999);
        do {
            if ($rand_num === 99999) {
                $rand_num = 0;
            }
            ++$rand_num;
            $order_number = date('YmdHis') . str_pad((string) $rand_num, 5, '0', STR_PAD_LEFT);
            $row = $this->model::query()->where(['order_number' => $order_number])->count();
        } while ($row);
        return $order_number;
    }
}
