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

namespace App\Crm\Mapper;

use App\Crm\Model\CrmShopOrder;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 订单管理Mapper类
 */
class CrmShopOrderMapper extends AbstractMapper
{
    /**
     * @var CrmShopOrder
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CrmShopOrder::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }

        // 商品 ID，关联商品表
        if (isset($params['shop_id']) && $params['shop_id'] !== '') {
            $query->where('shop_id', '=', $params['shop_id']);
        }

        // 订单编号，唯一标识
        if (isset($params['order_number']) && $params['order_number'] !== '') {
            $query->where('order_number', 'like', '%' . $params['order_number'] . '%');
        }

        // 订单金额
        if (isset($params['amount']) && $params['amount'] !== '') {
            $query->where('amount', '=', $params['amount']);
        }

        // 订单状态，0 - 待付款，1 - 已付款，2 - 已发货，3 - 已完成，4 - 已取消
        if (isset($params['order_status']) && $params['order_status'] !== '') {
            $query->where('order_status', '=', $params['order_status']);
        }

        // 订单类型
        if (isset($params['order_type']) && $params['order_type'] !== '') {
            $query->where('order_type', '=', $params['order_type']);
        }

        // 地址信息
        if (isset($params['address_id']) && $params['address_id'] !== '') {
            $query->where('address_id', '=', $params['address_id']);
        }

        // 物流公司
        if (isset($params['logistics_company']) && $params['logistics_company'] !== '') {
            $query->where('logistics_company', 'like', '%' . $params['logistics_company'] . '%');
        }

        // 物流单号
        if (isset($params['tracking_number']) && $params['tracking_number'] !== '') {
            $query->where('tracking_number', 'like', '%' . $params['tracking_number'] . '%');
        }

        // 订单备注
        if (isset($params['order_note']) && $params['order_note'] !== '') {
            $query->where('order_note', '=', $params['order_note']);
        }

        // 创建人
        if (isset($params['created_id']) && $params['created_id'] !== '') {
            $query->where('created_id', '=', $params['created_id']);
        }

        return $query;
    }
}
