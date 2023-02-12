<?php

declare(strict_types=1);

namespace App\Order\Mapper;

use App\Order\Model\UsersRenew;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

class UsersRenewMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = UsersRenew::class;
    }

    /**
     * 续费表插入数据.
     */
    public function insert(array $value): int
    {
        return UsersRenew::query()->insertGetId($value);
    }

    /**
     * 修改用户ID.
     * @param int $userId 用户id
     * @param int $orderId 订单id
     * @param int $newUserId 新用户id
     */
    public function transformUser(int $userId, int $orderId, int $newUserId, int $newOrderId): int
    {
        return $this->model::query()->where('user_id', $userId)
            ->where('order_id', $orderId)
            ->update(['user_id' => $newUserId, 'order_id' => $newOrderId]);
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['user_id'])) {
            $query->where('user_id', $params['user_id']);
        }
        if (isset($params['order_id'])) {
            $query->where('order_id', $params['order_id']);
        }
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }
        if (isset($params['audit_status'])) {
            $query->where('audit_status', $params['audit_status']);
        }
        // 关联订单表
        if (! empty('withOrder')) {
            $query->with(['order']);
        }
        // 关联课程表
        if (! empty('withCourse')) {
            $query->with('course:course_basis.id,course_basis.title,course_basis.price,course_basis.indate,course_basis.created_at,course_basis.subject_id,course_title,is_give');
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
        return $query;
    }
}
