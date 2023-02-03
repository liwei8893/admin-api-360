<?php

declare(strict_types=1);

namespace App\Order\Mapper;

use App\Order\Model\Order;
use App\Order\Model\OrderTransaction;
use App\Order\Model\UsersRenew;
use App\Users\Model\User;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractMapper;

/**
 * 订单管理Mapper类.
 */
class OrderStaMapper extends AbstractMapper
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
     * 新增统计.
     * @param mixed $params
     */
    public function getNewVipSta(array $params): Collection|array
    {
        $params['dateMonth'] = ! empty($params['dateMonth']) ? $params['dateMonth'] : date('Y-m');
        $firstDay = date('Y-m-01', strtotime($params['dateMonth']));
        $lastDay = date('Y-m-d', strtotime("{$firstDay} +1 month -1 day"));

        $query = User::query()->leftJoin('order', 'users.id', 'order.user_id')
            ->where('order.shop_id', 950)
            ->where('users.user_type', User::USER_TYPE)
            ->whereIn('order.pay_states', [2, 7])
            ->where('order.deleted_at', 0)
            ->where('order.status', '!=', 2)
            ->whereBetween(
                'order.created_at',
                [strtotime($firstDay . ' 00:00:00'), strtotime($lastDay . ' 23:59:59')]
            )
            ->select(['users.id', 'users.platform', 'users.mobile', 'order.created_at', 'order.shop_id', 'order.shop_name', 'order.indate'])
            ->orderBy('order.created_at')
            ->platformDataScope('users.platform')
            ->when(isset($params['platform']) && is_array($params['platform']), function ($query) use ($params) {
                $query->whereIn('users.platform', $params['platform']);
            });

        return $this->handleSearch($query, $params)->get();
    }

    /**
     * 续费统计
     * @param mixed $params
     */
    public function getRenewalSta($params): Collection|array
    {
        $params['dateMonth'] = $params['dateMonth'] ?? date('Y-m');
        $firstDay = date('Y-m-01', strtotime($params['dateMonth']));
        $lastDay = date('Y-m-d', strtotime("{$firstDay} +1 month -1 day"));

        $query = UsersRenew::query()
            ->leftJoin('order', 'users_renew.order_id', 'order.id')
            ->leftJoin('users', 'users.id', 'users_renew.user_id')
            ->whereHas('users', function (Builder $query) use ($params) {
                $query->platformDataScope()
                    ->where('user_type', User::USER_TYPE)
                    ->when(isset($params['platform']) && is_array($params['platform']), function (Builder $query) use ($params) {
                        $query->whereIn('platform', $params['platform']);
                    });
            })
            ->where('users_renew.audit_status', UsersRenew::AUDIT_SUCCESS)
            ->where('users_renew.status', UsersRenew::STATUS_RENEW)
            ->whereBetween(
                'users_renew.created_at',
                [strtotime($firstDay . ' 00:00:00'), strtotime($lastDay . ' 23:59:59')]
            )
            ->where('order.shop_id', 950)
            ->where('order.pay_states', 7)
            ->where('order.deleted_at', 0)
            ->where('order.status', '!=', 2)
            ->select(['users_renew.*', 'users.platform', 'users.mobile', 'order.shop_id', 'order.shop_name', 'order.indate'])
            ->orderBy('users_renew.created_at');
        return $this->handleSearch($query, $params)->get();
    }

    /**
     * 退费统计
     * @param mixed $params
     */
    public function getRefundSta($params): Collection|array
    {
        $params['dateMonth'] = $params['dateMonth'] ?? date('Y-m');
        $firstDay = date('Y-m-01', strtotime($params['dateMonth']));
        $lastDay = date('Y-m-d', strtotime("{$firstDay} +1 month -1 day"));

        $query = OrderTransaction::query()
            ->leftJoin('order', 'order_transaction.order_id', 'order.id')
            ->leftJoin('users', 'users.id', 'order_transaction.user_id')
            ->whereHas('users', function (Builder $query) use ($params) {
                $query->platformDataScope()
                    ->where('user_type', User::USER_TYPE)
                    ->when(isset($params['platform']) && is_array($params['platform']), function (Builder $query) use ($params) {
                        $query->whereIn('platform', $params['platform']);
                    });
            })
            ->where('order_transaction.type_id', OrderTransaction::TYPE_REFUND)
            ->whereBetween(
                'order_transaction.create_at',
                [$firstDay . ' 00:00:00', $lastDay . ' 23:59:59']
            )
            ->where('order.shop_id', 950)
            ->where('order.pay_states', 7)
            ->where('order.deleted_at', 0)
            ->select(['order_transaction.*', 'users.platform', 'users.mobile', 'order.shop_id', 'order.shop_name', 'order.indate'])
            ->orderBy('order_transaction.create_at');
        return $this->handleSearch($query, $params)->get();
    }
}
