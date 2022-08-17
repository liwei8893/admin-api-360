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

    public function assignModel()
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

        // 订单ID
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        // 用户ID
        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }

        // 商品ID
        if (isset($params['shop_id']) && $params['shop_id'] !== '') {
            $query->where('shop_id', '=', $params['shop_id']);
        }

        // 订单编号(用户看,不可随意更改)
        if (isset($params['order_number']) && $params['order_number'] !== '') {
            $query->where('order_number', '=', $params['order_number']);
        }

        // 支付编号 弃用(使用order_payments 支付单表)
        if (isset($params['pay_number']) && $params['pay_number'] !== '') {
            $query->where('pay_number', '=', $params['pay_number']);
        }

        // 支付状态:1:未支付 2:已支付 3:已取消 4:已删除 5:退款中 6:已退款 7:已完成
        if (isset($params['pay_states']) && $params['pay_states'] !== '') {
            $query->where('pay_states', '=', $params['pay_states']);
        }

        // 删除时间
        if (isset($params['deleted_at']) && $params['deleted_at'] !== '') {
            $query->where('deleted_at', '=', $params['deleted_at']);
        }

        // 创建时间
        if (isset($params['created_at']) && $params['created_at'] !== '') {
            $query->where('created_at', '=', $params['created_at']);
        }

        // 修改时间
        if (isset($params['updated_at']) && $params['updated_at'] !== '') {
            $query->where('updated_at', '=', $params['updated_at']);
        }

        // 订单备注
        if (isset($params['remark']) && $params['remark'] !== '') {
            $query->where('remark', '=', $params['remark']);
        }

        return $query;
    }
}