<?php

declare(strict_types=1);

namespace App\Order\Mapper;

use App\Order\Model\OrderSummary;
use App\Order\Model\OrderSummaryAdmin;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 核单记录Mapper类.
 */
class OrderSummaryMapper extends AbstractMapper
{
    /**
     * @var OrderSummary
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = OrderSummary::class;
    }

    public function setSummaryAdmin(array $params): bool
    {
        return (bool) OrderSummaryAdmin::query()->updateOrInsert(
            ['order_id' => $params['orderId']],
            ['admin_id' => $params['adminId']],
        );
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
        if (isset($params['type']) && ! is_array($params['type'])) {
            $query->where('type', $params['type']);
        }
        // 用户等级
        if (isset($params['level']) && $params['level'] !== '') {
            $query->where('level', '=', $params['level']);
        }

        // 是否添加微信
        if (isset($params['has_wechat']) && $params['has_wechat'] !== '') {
            $query->where('has_wechat', '=', $params['has_wechat']);
        }

        // 是否接通电话
        if (isset($params['has_connect']) && $params['has_connect'] !== '') {
            $query->where('has_connect', '=', $params['has_connect']);
        }

        if (isset($params['created_at'][0], $params['created_at'][1])) {
            $query->whereBetween(
                'created_at',
                [strtotime($params['created_at'][0] . ' 00:00:00'), strtotime($params['created_at'][1] . ' 23:59:59')]
            );
        }

        if (! empty($params['withUser'])) {
            $query->with('user');
        }
        $query->whereHas('user', function (Builder $query) use ($params) {
            $query->when(! empty($params['user_mobile']), function ($subQuery) use ($params) {
                $subQuery->where('mobile', 'like', "{$params['user_mobile']}%");
            })
                ->when(! empty($params['user_platform']), function ($subQuery) use ($params) {
                    $subQuery->where('platform', '=', $params['user_platform']);
                });
        });
        if (! empty($params['withAdminUser'])) {
            $query->with('adminUser');
        }
        if (isset($params['created_name'])) {
            $query->whereHas('adminUser', function (Builder $query) use ($params) {
                $query->where('nickname', 'like', "%{$params['created_name']}%");
            });
        }

        return $query;
    }
}
