<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\Order\Mapper;

use App\Order\Model\Order;
use App\Users\Model\Users;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractMapper;

/**
 * 订单管理Mapper类
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
     * 按月份获取各平台订单数据
     * @param $params
     * @return Collection|array
     */
    public function getStaByPlatform($params): Collection|array
    {
        $params['dateMonth'] = $params['dateMonth'] ?? date('Y-m');
        $firstDay = date('Y-m-01', strtotime($params['dateMonth']));
        $lastDay = date('Y-m-d', strtotime("$firstDay +1 month -1 day"));

        $query = Users::query()->leftJoin('order', 'users.id', 'order.user_id')
            ->where('order.shop_id', 950)
            ->where('users.user_type', 1)
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

}