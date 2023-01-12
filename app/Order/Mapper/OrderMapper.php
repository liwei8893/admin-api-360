<?php

declare(strict_types=1);

namespace App\Order\Mapper;

use App\Order\Model\Order;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractMapper;

/**
 * 订单管理Mapper类.
 */
class OrderMapper extends AbstractMapper
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
     * 有效期增加.
     * @param array $update 可选,更新order表其他字段
     * @return int
     *             author:ZQ
     *             time:2022-08-18 15:26
     */
    public function incrementInDate(int $id, int $day, array $update = []): int
    {
        return Order::query()->where('id', $id)->increment('indate', $day, $update);
    }

    /**
     * 有效期减少.
     * @param array $update 可选,更新order表其他字段
     * @return int
     *             author:ZQ
     *             time:2022-08-18 15:27
     */
    public function decrementInDate(int $id, int $day, array $update = []): int
    {
        return Order::query()->where('id', $id)->decrement('indate', $day, $update);
    }

    /**
     * 软删除.
     * @param mixed $id
     */
    public function softDelete($id): int
    {
        return $this->model::query()->where('id', $id)
            ->update(['deleted_at' => time()]);
    }

    /**
     * 返回数据集合.
     */
    public function getCollectByIds(array $ids, array $column = ['*']): Collection
    {
        return Order::query()->whereIn('id', $ids)->noDeleteOrder()->get($column);
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['user_id'])) {
            $query->where('user_id', $params['user_id']);
        }

        if (isset($params['status']) && ! is_array($params['status'])) {
            $query->where('status', $params['status']);
        }
        if (isset($params['status']) && is_array($params['status'])) {
            $query->whereIn('status', $params['status']);
        }

        if (isset($params['pay_states']) && ! is_array($params['pay_states'])) {
            $query->where('pay_states', $params['pay_states']);
        }

        if (isset($params['pay_states']) && is_array($params['pay_states'])) {
            $query->whereIn('pay_states', $params['pay_states']);
        }

        if (isset($params['audit_status']) && ! is_array($params['audit_status'])) {
            $query->where('audit_status', $params['audit_status']);
        }

        if (isset($params['audit_status']) && is_array($params['audit_status'])) {
            $query->whereIn('audit_status', $params['audit_status']);
        }

        if (isset($params['shop_name'])) {
            $query->where('shop_name', 'like', "%{$params['shop_name']}%");
        }
        if (isset($params['shop_id'])) {
            $query->where('shop_id', $params['shop_id']);
        }
        if (isset($params['pay_type'])) {
            $query->where('pay_type', $params['pay_type']);
        }

        if (isset($params['normalOrder']) && $params['normalOrder']) {
            $query->normalOrder();
        }

        if (isset($params['noDeleteOrder']) && $params['noDeleteOrder']) {
            $query->noDeleteOrder();
        }

        // 关联续费表
        if (! empty($params['withRenew'])) {
            $query->with('usersRenew');
        }

        // 关联订单年级
        if (! empty($params['withOrderGrade'])) {
            $query->with('orderGrade');
        }

        // 关联订单科目
        if (! empty($params['withOrderSubject'])) {
            $query->with('orderSubject');
        }

        if (! empty($params['withCourse'])) {
            $query->with('course:id,title,price,indate,created_at,subject_id,course_title,is_give');
        }

        // 关联付款表
        if (! empty($params['withPayment'])) {
            $query->with('payment');
        }
        if (isset($params['payment_number'])) {
            $query->whereHas('payment', function (Builder $query) use ($params) {
                $query->when(isset($params['payment_number']), function (Builder $query) use ($params) {
                    $query->where('payment_number', 'like', "%{$params['payment_number']}%");
                });
            });
        }

        // 关联用户表
        if (! empty($params['withUsers'])) {
            $query->with('users:id,user_name,mobile,platform,old_platform,user_type,status');
        }
        $query->whereHas('users', function ($query) use ($params) {
            $query->platformDataScope()
                ->when(isset($params['users_user_type']), function ($query) use ($params) {
                    $query->where('user_type', $params['users_user_type']);
                })
                ->when(isset($params['users_mobile']), function ($query) use ($params) {
                    $query->where('mobile', 'like', "{$params['users_mobile']}%");
                })->when(isset($params['users_platform']), function ($query) use ($params) {
                    if (is_array($params['users_platform'])) {
                        $query->whereIn('platform', $params['users_platform']);
                    } else {
                        $query->where('platform', $params['users_platform']);
                    }
                });
        });
        if (isset($params['created_at'][0], $params['created_at'][1])) {
            $query->whereBetween(
                'created_at',
                [strtotime($params['created_at'][0] . ' 00:00:00'), strtotime($params['created_at'][1] . ' 23:59:59')]
            );
        }
        if (isset($params['created_at_time'][0], $params['created_at_time'][1])) {
            $query->whereBetween(
                'created_at',
                [strtotime($params['created_at_time'][0]), strtotime($params['created_at_time'][1])]
            );
        }
        if (isset($params['course_end_time'][0], $params['course_end_time'][1])) {
            $startTime = $params['course_end_time'][0] . ' 00:00:00';
            $endTime = $params['course_end_time'][1] . ' 23:59:59';
            $query->whereRaw("created_at + (indate * 86400) > UNIX_TIMESTAMP('{$startTime}')");
            $query->whereRaw("created_at + (indate * 86400) < UNIX_TIMESTAMP('{$endTime}')");
        }
        return $query;
    }
}
