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
use Hyperf\Database\Model\Collection;
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

    public function assignModel(): void
    {
        $this->model = Order::class;
    }

    /**
     * 有效期增加
     * @param int $id
     * @param int $day
     * @param array $update 可选,更新order表其他字段
     * @return int
     * author:ZQ
     * time:2022-08-18 15:26
     */
    public function incrementInDate(int $id, int $day, array $update = []): int
    {
      return  Order::query()->where('id', $id)->increment('indate', $day, $update);
    }

    /**
     * 有效期减少
     * @param int $id
     * @param int $day
     * @param array $update 可选,更新order表其他字段
     * @return int
     * author:ZQ
     * time:2022-08-18 15:27
     */
    public function decrementInDate(int $id, int $day, array $update = []): int
    {
        return  Order::query()->where('id', $id)->decrement('indate', $day, $update);
    }

    /**
     * 软删除
     * @param $id
     * @return int
     * author:ZQ
     * time:2022-08-21 11:50
     */
    public function softDelete($id): int
    {
        return $this->model::query()->where('id',$id)
            ->update(['deleted_at' =>time()]);
    }

    /**
     * 返回数据集合
     * @param array $ids
     * @param array $column
     * @return Collection
     * author:ZQ
     * time:2022-08-18 17:52
     */
    public function getCollectByIds(array $ids, array $column = ['*']): Collection
    {
        return Order::query()->whereIn('id',$ids)->noDeleteOrder()->get($column);
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

        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (isset($params['shop_name'])) {
            $query->where('shop_name', 'like', "%{$params['shop_name']}%");
        }

        if (isset($params['normalOrder']) && $params['normalOrder']) {
            $query->normalOrder();
        }

        if (isset($params['noDeleteOrder']) && $params['noDeleteOrder']) {
            $query->noDeleteOrder();
        }

        //关联续费表
        if (!empty($params['withRenew'])){
            $query->with('usersRenew');
        }

        // 关联订单年级
        if (!empty($params['withOrderGrade'])){
            $query->with('orderGrade');
        }
        // 关联订单科目
        if (!empty($params['withOrderSubject'])){
            $query->with('orderSubject');
        }

        if (isset($params['created_at'][0], $params['created_at'][1])) {
            $query->whereBetween(
                'created_at',
                [strtotime($params['created_at'][0] . ' 00:00:00'), strtotime($params['created_at'][1] . ' 23:59:59')]
            );
        }
        if (isset($params['course_end_time'][0], $params['course_end_time'][1])) {
            $startTime = $params['course_end_time'][0] . ' 00:00:00';
            $endTime = $params['course_end_time'][1] . ' 23:59:59';
            $query->whereRaw("created_at + (indate * 86400) > UNIX_TIMESTAMP('$startTime')");
            $query->whereRaw("created_at + (indate * 86400) < UNIX_TIMESTAMP('$endTime')");
        }
        return $query;
    }
}