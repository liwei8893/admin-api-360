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
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 订单管理Mapper类
 */
class OrderMapper extends AbstractMapper
{
    /**
     * @var Order
     */
    public $model;

    public function assignModel():void
    {
        $this->model = Order::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        if (isset($params['user_id'])) {
            $query->where('user_id', $params['user_id']);
        }

        if (isset($params['normalOrder']) && $params['normalOrder']) {
            $query->normalOrder();
        }

        if (isset($params['noDeleteOrder']) && $params['noDeleteOrder']) {
            $query->noDeleteOrder();
        }
        if (isset($params['created_at'], $params['created_at'])) {
            $query->whereBetween(
                'created_at',
                [strtotime($params['created_at'] . ' 00:00:00'), strtotime($params['created_at'] . ' 23:59:59')]
            );
        }
        return $query;
    }
}