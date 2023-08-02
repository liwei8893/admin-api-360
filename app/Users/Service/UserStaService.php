<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\Users\Model\User;
use Mine\Abstracts\AbstractService;

class UserStaService extends AbstractService
{
    public function getArealDistribution(array $params): array
    {
        $parent_id = $params['parent_id'];
        $modelData = User::query()
            ->leftJoin('order', 'users.id', 'order.user_id')
            ->platformDataScope('users.platform')
            ->whereIn('order.shop_id', [User::VIP_TYPE_SUPER, ...User::VIP_TYPE_HIGH])
            ->where('order.pay_states', 7)
            ->where('order.status', '!=', 2)
            ->where('order.deleted_at', 0)
            ->where('users.user_type', 1)
            ->where('users.province_id', '!=', 0)
            ->whereNotNull('users.province_id')
            ->whereNotNull('users.city_id')
            ->whereNotNull('area.area_name')
            ->groupBy(['area.area_name', 'area.id'])
            ->select(['area.area_name as name', 'area.id'])
            ->selectRaw('count(area_name) as value')
            ->orderBy('value', 'desc');
        if (! empty($params['parent_id'])) {
            $modelData->leftJoin('area', 'users.city_id', 'area.id')
                ->where('area.parent_id', $parent_id);
        } else {
            $modelData->leftJoin('area', 'users.province_id', 'area.id');
        }
        return $modelData->get()->toArray();
    }
}
