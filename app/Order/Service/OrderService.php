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

namespace App\Order\Service;

use App\Order\Mapper\OrderMapper;
use App\Users\Service\UsersService;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;

/**
 * 订单管理服务类
 */
class OrderService extends AbstractService
{
    /**
     * @var OrderMapper
     */
    #[inject]
    public $mapper;

    #[inject]
    public UsersRenewService $usersRenewService;

    #[inject]
    public UsersService $usersService;

    #[inject]
    public OrderTransactionService $orderTransactionService;

    /**
     * 课程购买记录
     * @param $data
     * @return array
     * author:ZQ
     * time:2022-09-20 13:52
     */
    public function getBuyRecordList($data): array
    {
        return $this->mapper->getBuyRecordList($data);
    }

    /**
     * 批量修改有效期
     * @param $params
     * author:ZQ
     * time:2022-08-18 15:35
     */
    #[Transaction]
    public function changeEndDate($params): bool
    {
        if (empty($params['day']) && in_array($params['type'], [1, 2], true)) {
            throw new NormalStatusException('参数错误:未选择有效期天数!');
        }
        if (empty($params['date']) && $params['type'] === 3) {
            throw new NormalStatusException('参数错误:未选择指定有效期!');
        }
        // 是否续费
        $query = [];
        if (!empty($params['renew']) && !empty($params['money'])) {
            $query = ['is_renew' => 1, 'renew_time' => time()];
        }
        $orderData = $this->mapper->getCollectByIds($params['ids'], ['id', 'created_at', 'indate', 'user_id', 'shop_id']);
        foreach ($orderData as $item) {
            // 增加有效期
            if ($params['type'] === 1) {
                $params['date'] = Carbon::parse($item->course_end_time)->addDays($params['day'])->toDateString();
            }
            // 减少有效期
            if ($params['type'] === 2) {
                $params['date'] = Carbon::parse($item->course_end_time)->addDays(-$params['day'])->toDateString();
            }
            // 修改到指定有效期
            $update = $this->handleEndDateToTime($item->toArray(), $params['date'], $query);
            // 插入续费记录
            $insert = $this->handleRenewData($item->toArray(), $params);
            if (!$update || !$insert) {
                throw new NormalStatusException('更新失败,请稍后重试!');
            }
        }
        return true;
    }

    /**
     * 处理指定有效期的更改
     * @param array $item
     * @param string $endDate
     * @param array $query
     * author:ZQ
     * time:2022-08-19 14:34
     * @return int
     */
    public function handleEndDateToTime(array $item, string $endDate, array $query = []): int
    {
        $oldDt = Carbon::parse(Carbon::parse($item['course_end_time'])->toDateString());
        $newDt = Carbon::parse($endDate);
        // 计算相差多少天
        $diffDay = $oldDt->diffInDays($newDt);
        // 计算出时间是加还是减,lte小于或等于
        $hasAdd = $oldDt->lte($newDt);
        if ($hasAdd) {
            return $this->mapper->incrementInDate($item['id'], $diffDay, $query);
        }
        return $this->mapper->decrementInDate($item['id'], $diffDay, $query);
    }


    /**
     * 组装数据,保存续费记录表
     * @param array $item
     * @param array $params
     * @return bool
     * author:ZQ
     * time:2022-08-19 16:06
     */
    public function handleRenewData(array $item, array $params): bool
    {
        $data = [
            'status' => $params['renew'] ? 1 : 0,
            'startDate' => $item['course_end_time'],
            'endDate' => $params['date'],
        ];
        return $this->usersRenewService->recordUserRenew(array_merge($item, $params, $data));
    }

    /**
     * @param $data
     * @return bool
     * author:ZQ
     * time:2022-08-21 11:26
     */
    #[Transaction]
    public function changeOrderToUser($data): bool
    {
        //oldUserId newUserId orderId
        // 复制模型修改备注
        $orderModel = $this->mapper->read($data['orderId']);
        if (!$orderModel) {
            throw new NormalStatusException('订单错误!');
        }
        $data['remark'] = "从{$data['oldUserId']}转入";
        $copyOrderModel = $orderModel->replicate()->fill([
            'remark' => $data['remark'],
            'created_at' => $orderModel->created_at,
            'updated_at' => time(),
            'user_id' => $data['newUserId'],
        ]);
        $copyOrderModel->timestamps = false;
        $copyOrderModel->save();
        $newOrderId = $copyOrderModel->id;
        // 软删除老订单数据
        if (!$this->mapper->softDelete($orderModel->id)) {
            throw new NormalStatusException('订单更新失败,请稍后重试!');
        }
        // 写日志
        $logRecord = [
            'oldUserId' => $data['oldUserId'],
            'newUserId' => $data['newUserId'],
            'oldOrderId' => $data['orderId'],
            'remark' => $data['remark'],
            'newOrderId' => $newOrderId
        ];
        if (!$this->orderTransactionService->OrderToUserRecord($logRecord)) {
            throw new NormalStatusException('日志写入错误,操作已回滚,请稍后重试!');
        }
        return true;
    }

    /**
     * 转班
     * author:ZQ
     * time:2022-08-20 11:46
     */
    #[Transaction]
    public function changeOrderToCourse($data)
    {
        $orderModel = $this->mapper->read($data['orderId']);
        if (!$orderModel) {
            throw new NormalStatusException('订单错误!');
        }
        return true;
    }

    /**
     * 退费
     * author:ZQ
     * time:2022-08-20 11:46
     */
    #[Transaction]
    public function changeOrderToRefund($data)
    {
        $orderModel = $this->mapper->read($data['orderId']);
        if (!$orderModel) {
            throw new NormalStatusException('订单错误!');
        }
        $orderModel->status = 2;
        $orderModel->refund_time = time();
        if (!$orderModel->save()) {
            throw new NormalStatusException('退费失败!');
        }
        // 写日志
        $logRecord = [
            'order_id' => $data['orderId'],
            'user_id' => $data['userId'],
            'money' => $data['money'],
            'remark' => $data['remark'],
        ];
        if (!$this->orderTransactionService->OrderToRefundRecord($logRecord)) {
            throw new NormalStatusException('日志写入错误,操作已回滚,请稍后重试!');
        }
        return true;
    }

    /**
     * 处理导出数据
     * @param array $data
     * author:ZQ
     * time:2022-11-12 18:29
     */
    protected function handleExportData(array &$data): void
    {
        $statusMap = [0 => '暂停', 1 => '正常', 2 => '退费'];
        $data['status'] = $statusMap[$data['status']] ?? '未知';
        $tagTypeMap = [1 => 'PC', 4 => 'H5', 6 => '微信内置H5'];
        $data['tag_type'] = $tagTypeMap[$data['tag_type']] ?? '未知';
        $payTypeMap = [1 => '微信', 6 => '管理员赠送'];
        $data['pay_type'] = $payTypeMap[$data['pay_type']] ?? '未知';
        $data['payment_number'] = !empty($data['payment']) ? implode(',', array_column($data['payment'], 'payment_number')) : '';
        $data['order_grade'] = !empty($data['order_grade']) ? implode(',', array_column($data['order_grade'], 'title')) : '';
        $data['order_subject'] = !empty($data['order_subject']) ? implode(',', array_column($data['order_subject'], 'title')) : '';
    }

}