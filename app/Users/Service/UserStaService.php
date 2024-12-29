<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\Course\Model\CourseBasis;
use App\Users\Model\User;
use Mine\Abstracts\AbstractService;

class UserStaService extends AbstractService
{
    /**
     * 获取区域分布情况
     *
     * 根据提供的参数，查询用户订单数据，以区域名称和订单数量的形式返回区域分布情况。
     * 支持根据父级区域ID筛选，若提供父级区域ID，则返回该父级区域下的子区域分布；若未提供，则返回省份级别的区域分布。
     *
     * @param array $params 包含查询参数的数组，可选包含 'parent_id' 键来指定父级区域ID。
     * @return array 返回一个数组，每个元素包含区域名称('name')、区域ID('id')和该区域的订单数量('value')。
     */
    public function getArealDistribution(array $params): array
    {
        // 初始化父级区域ID
        $parent_id = $params['parent_id'] ?? null;

        $subQueryShopId = CourseBasis::query()->select('id')->where('course_title', 64);

        // 构建初始的查询模型，设置查询范围和条件
        $modelData = User::query()
            ->leftJoin('order', 'users.id', 'order.user_id')
            ->platformDataScope('users.platform')
            ->whereIn('order.shop_id', $subQueryShopId)
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
        // 根据是否提供了父级ID来调整查询条件，以获取正确的区域层级数据
        if (!empty($params['parent_id'])) {
            $modelData->leftJoin('area', 'users.city_id', 'area.id')
                ->where('area.parent_id', $parent_id);
        } else {
            $modelData->leftJoin('area', 'users.province_id', 'area.id');
        }
        // 执行查询并返回结果数组
        return $modelData->get()->toArray();
    }
}
