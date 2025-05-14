<?php
declare(strict_types=1);

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
        $query->whereHas('user', function ($query) use ($params) {
            $query->platformDataScope();
        });

        if (!empty($params['withShop'])) {
            $query->with('shop');
        }
        if (!empty($params['withUser'])) {
            $query->with('user:id,user_name,mobile,platform');
        }
        if (!empty($params['withAddress'])) {
            $query->with('address');
        }
        if (!empty($params['withAdmin'])) {
            $query->with('admin:id,nickname');
        }

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

        // 任务类型
        if (isset($params['task_type']) && $params['task_type'] !== '') {
            $query->where('task_type', '=', $params['task_type']);
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
        if (isset($params['created_by']) && $params['created_by'] !== '') {
            $query->where('created_by', '=', $params['created_by']);
        }

        // 创建时间
        if (isset($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) === 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0], $params['created_at'][1]]
            );
        }

        return $query;
    }
}
