<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\Users\Model\User;
use Mine\Abstracts\AbstractService;

class UserStaService extends AbstractService
{
    public function getArealDistribution(): array
    {
        $modelData = User::query()
            ->leftJoin('order', 'users.id', 'order.user_id')
            ->leftJoin('area', 'users.province_id', 'area.id')
            ->platformDataScope('users.platform')
            ->where('order.shop_id', 950)
            ->where('order.pay_states', 7)
            ->where('order.status', '!=', 2)
            ->where('order.deleted_at', 0)
            ->where('users.user_type', 1)
            ->where('users.province_id', '!=', 0)
            ->whereNotNull('users.province_id')
            ->whereNotNull('area.area_name')
            ->groupBy(['area.area_name'])
            ->select(['area.area_name as name'])
            ->selectRaw('count(area_name) as value')
            ->orderBy('value', 'desc')
            ->get();
        return $modelData->toArray();
    }
}
