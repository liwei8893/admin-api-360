<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Course\Model\CourseBasis;
use App\Crm\Service\CrmUserTimelineService;
use App\Order\Mapper\OrderMapper;
use App\Order\Model\Order;
use App\Order\Model\UsersRenew;
use App\Score\Event\ScoreAddEvent;
use App\System\Model\SystemRole;
use App\System\Service\SystemQueueMessageService;
use App\Users\Model\User;
use App\Users\Service\UserScoreService;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * 订单管理服务类.
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
    public OrderTransactionService $orderTransactionService;

    #[Inject]
    protected UserScoreService $userScoreService;

    #[Inject]
    protected SystemQueueMessageService $queueMessageService;

    #[Inject]
    protected CrmUserTimelineService $crmUserTimelineService;

    /**
     * 批量修改有效期
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Transaction]
    public function changeEndDate($params): bool
    {
        // $params['type'] 1:有效期增加,2:有效期减少,3:指定有效期
        $params['type'] = (int)$params['type'];
        if (empty($params['month']) && in_array($params['type'], [1, 2], true)) {
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
        /** @var Order $item */
        foreach ($orderData as $item) {
            // 判断是否有待审核订单,有的话不能进行新的操作
            $hasRenew = $item->usersRenew()->where('audit_status', UsersRenew::AUDIT_PENDING)->exists();
            if ($hasRenew) {
                throw new NormalStatusException('有订单在审核中,请先处理!');
            }
            // 增加有效期,判断到期时间是否大于当前日期,如果已经过期从当前日期计算
            if ($params['type'] === 1) {
                $endDate = Carbon::parse($item->course_end_time);
                // 检查结束日期是否已过去
                if ($endDate->isPast()) {
                    $params['date'] = Carbon::now()->addMonths($params['month'])->toDateString();
                } else {
                    $params['date'] = $endDate->addMonths($params['month'])->toDateString();
                }
            }
            // 减少有效期
            if ($params['type'] === 2) {
                $params['date'] = Carbon::parse($item->course_end_time)->addMonths(-$params['month'])->toDateString();
            }
            // 修改到指定有效期
            $update = $this->handleEndDateToTime($item->toArray(), $params['date'], $query);
            // 插入续费记录
            $insertId = $this->handleRenewData($item->toArray(), $params);
            if (!$update || !$insertId) {
                throw new NormalStatusException('更新失败,请稍后重试!');
            }
            // TODO 续费时增加积分,不需要审核
            $isNoAudit = user()->isNoAuditRole();
            if ($isNoAudit && $item->shop_id === User::VIP_TYPE_SUPER) {
                event(new ScoreAddEvent('renew', $item->user_id, $insertId));
            }
            // 需要审核,发送站内信
            if (!$isNoAudit) {
                // 发送站内消息
                try {
                    $adminUsers = SystemRole::find(3)->users;
                    $this->queueMessageService->sendMessage(['title' => '续费审核', 'content' => '有续费待审核,请查看!', 'users' => [...$adminUsers->pluck('id')->toArray(), 2]]);
                } catch (ContainerExceptionInterface|NotFoundExceptionInterface|Throwable $e) {
                }
            }
        }
        return true;
    }

    /**
     * 处理指定有效期的更改.
     */
    public function handleEndDateToTime(array $item, string $endDate, array $query = []): bool
    {
        $oldDt = Carbon::parse(Carbon::parse($item['course_end_time'])->toDateString());
        $newDt = Carbon::parse($endDate);
        // 计算相差多少天
        $diffDay = $oldDt->diffInDays($newDt);
        // 计算出时间是加还是减,lte小于或等于
        $hasAdd = $oldDt->lte($newDt);
        // 是否需要审核
        $isAudit = user()->isNoAuditRole();
        // 需要审核不直接修改
        if (!$isAudit) {
            return true;
        }
        if ($hasAdd) {
            return (bool)$this->mapper->incrementInDate($item['id'], $diffDay, $query);
        }
        return (bool)$this->mapper->decrementInDate($item['id'], $diffDay, $query);
    }

    /**
     * 组装数据,保存续费记录表.
     */
    public function handleRenewData(array $item, array $params): int
    {
        $data = [
            'status' => $params['renew'] ? 1 : 0,
            'startDate' => $item['course_end_time'],
            'endDate' => $params['date'],
            'real_year' => $params['real_year'],
        ];
        return $this->usersRenewService->recordUserRenew(array_merge($item, $params, $data));
    }

    #[Transaction]
    public function changeOrderToUser($data): bool
    {
        // oldUserId newUserId orderId
        // 复制模型修改备注
        /** @var null|Order $orderModel */
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
            'newOrderId' => $newOrderId,
        ];
        if (!$this->orderTransactionService->OrderToUserRecord($logRecord)) {
            throw new NormalStatusException('日志写入错误,操作已回滚,请稍后重试!');
        }
        return true;
    }

    /**
     * 转班.
     */
    #[Transaction]
    public function changeOrderToCourse($data): bool
    {
        $orderModel = $this->mapper->read($data['orderId']);
        if (!$orderModel) {
            throw new NormalStatusException('订单错误!');
        }
        return true;
    }

    /**
     * 批量退费.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function batchChangeOrderToRefund(array $params): bool
    {
        $orderIds = $params['ids'];
        foreach ($orderIds as $orderId) {
            $this->changeOrderToRefund(['orderId' => $orderId, 'money' => 0, 'remark' => '退费']);
        }
        return true;
    }

    /**
     * 退费.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Transaction]
    public function changeOrderToRefund($data): bool
    {
        /** @var null|Order $orderModel */
        $orderModel = $this->mapper->read($data['orderId']);
        if (!$orderModel) {
            throw new NormalStatusException('订单错误!');
        }
        if ($orderModel->status === Order::STATUS_REFUND) {
            throw new NormalStatusException('订单已退费，请检查!');
        }
        $orderModel->status = Order::STATUS_REFUND;
        $orderModel->refund_time = Carbon::now();
        if (!$orderModel->save()) {
            throw new NormalStatusException('退费失败!');
        }
        // 写日志
        $logRecord = [
            'order_id' => $data['orderId'],
            'user_id' => $orderModel->user_id,
            'money' => $data['money'],
            'remark' => $data['remark'],
        ];
        if (!$this->orderTransactionService->OrderToRefundRecord($logRecord)) {
            throw new NormalStatusException('日志写入错误,操作已回滚,请稍后重试!');
        }
        // TODO 退费扣除积分
        if ($orderModel->shop_id === User::VIP_TYPE_SUPER) {
            event(new ScoreAddEvent('courseRefund', $orderModel->user_id, $orderModel->id));
        }
        return true;
    }

    public function changeOrderToNormal($data): bool
    {
        /** @var null|Order $orderModel */
        $orderModel = $this->mapper->read($data['orderId']);
        if (!$orderModel) {
            throw new NormalStatusException('订单错误!');
        }
        // 暂停恢复计算暂停时间,补上.去掉状态时间
        if ($orderModel->status === 0) {
            $nowDate = Carbon::now();
            $diffDay = $orderModel->status_time->diffInDays($nowDate);
            $orderModel->indate += $diffDay;
            $orderModel->status_time = null;
        }
        // 退费恢复退费时间去掉,
        if ($orderModel->status === 2) {
            $orderModel->refund_time = null;
        }
        $orderModel->status = Order::STATUS_NORMAL;
        if (!$orderModel->save()) {
            throw new NormalStatusException('恢复状态失败!');
        }
        return true;
    }

    public function changeOrderToPause($data): bool
    {
        /** @var null|Order $orderModel */
        $orderModel = $this->mapper->read($data['orderId']);
        if (!$orderModel) {
            throw new NormalStatusException('订单错误!');
        }
        $orderModel->status = Order::STATUS_PAUSE;
        $orderModel->status_time = Carbon::now();
        if (!$orderModel->save()) {
            throw new NormalStatusException('恢复状态失败!');
        }
        return true;
    }

    /**
     * 删除订单.
     */
    public function changeOrderToDelete(array $data): bool
    {
        /** @var null|Order $orderModel */
        $orderModel = $this->mapper->read($data['orderId']);
        if (!$orderModel) {
            throw new NormalStatusException('订单错误!');
        }
        $orderModel->deleted_at = time();
        if (!$orderModel->save()) {
            throw new NormalStatusException('订单删除失败!');
        }
        return true;
    }

    /**
     * 获取审核列表.
     */
    public function orderList(array $data): array
    {
        $data['withOrderGrade'] = true;
        $data['withOrderSubject'] = true;
        $data['withUsers'] = true;
        $data['withCourse'] = true;
        $data['orderBy'] = ['id'];
        $data['orderType'] = ['desc'];
        $data['pay_states'] = Order::PAY_AUDIT;
        return $this->mapper->getPageList($data);
    }

    /**
     * 审核报名信息.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Transaction]
    public function auditOrder(array $params): bool
    {
        // 获取订单课程id,后续判断是否是订单课程
        $orderCourseIds = CourseBasis::query()->select('id')->where('course_title', 64)->pluck('id');
        foreach ($params['ids'] as $id) {
            /* @var Order $model */
            $model = $this->mapper->read($id);
            if (!$model) {
                continue;
            }
            // 状态不为待审核跳过
            if ($model->pay_states !== Order::PAY_AUDIT) {
                continue;
            }
            // 审核通过
            if ($params['pay_states'] === Order::PAY_SUCCESS) {
                $model->pay_states = Order::PAY_SUCCESS;
            } else {
                // 审核不通过
                $model->pay_states = Order::PAY_REJECT;
                $model->deleted_at = time();
            }
            $model->cause_text = $params['cause_text'] ?? '';
            $model->save();
            // TODO 增加积分 init 新增会员审核完毕时
            if ($model->pay_states === Order::PAY_SUCCESS && $orderCourseIds->contains($model->shop_id)) {
                event(new ScoreAddEvent('init', $model->user_id, $model->id));
                // 保存crm用户时间线
                $adminId = user()->getId();
                $adminName = user()->getNickname();
                $this->crmUserTimelineService->saveRegisterCourseEvent($model->user_id, $adminId, "管理员[{$adminName}]报名[{$model->shop_name}]课程");
            }
        }
        return true;
    }

    /**
     * 编辑订单价格,实际年数.
     */
    #[Transaction]
    public function editOrder(array $params): bool
    {
        /** @var Order $orderModel */
        $orderModel = $this->mapper->read($params['orderId']);
        if (!$orderModel) {
            throw new NormalStatusException('订单不存在');
        }
        $orderPrice = (int)$orderModel->actual_price;
        $newPrice = (int)$params['actual_price'];
        // 修改报名金额
        if ($newPrice !== $orderPrice) {
            // 要变更的积分
            $diffScore = $newPrice - $orderPrice;
            $this->userScoreService->changeScore([
                'user_id' => $orderModel->user_id,
                'origin_id' => $orderModel->id,
                'channel' => '管理员操作',
                'channel_type' => 0,
                'score' => abs($diffScore),
                'type' => $diffScore > 0 ? 1 : 0,
            ]);
            // 修改金额
            $orderModel->actual_price = $newPrice;
        }
        $orderModel->real_year = $params['real_year'];
        if (isset($params['chapter_count_auth'])) {
            $orderModel->chapter_count_auth = $params['chapter_count_auth'];
        }
        return $orderModel->save();
    }

    /**
     * 处理导出数据.
     */
    protected function handleExportData(array &$data): void
    {
        $statusMap = [0 => '暂停', 1 => '正常', 2 => '退费'];
        $data['status'] = $statusMap[$data['status']] ?? '未知';
        $tagTypeMap = [1 => 'PC', 4 => 'H5', 6 => '微信内置H5'];
        $data['tag_type'] = $tagTypeMap[$data['tag_type']] ?? '未知';
        $payTypeMap = [1 => '微信', 6 => '管理员赠送'];
        $data['pay_type'] = $payTypeMap[$data['pay_type']] ?? '未知';
        $data['created_at'] = date('Y-m-d h:m:s', (int)$data['created_at']);
        $data['payment_number'] = !empty($data['payment']) ? implode(',', array_column($data['payment'], 'payment_number')) : '';
        $data['order_grade'] = !empty($data['order_grade']) ? implode(',', array_column($data['order_grade'], 'title')) : '';
        $data['order_subject_count'] = count($data['order_subject']);
        $data['order_subject'] = !empty($data['order_subject']) ? implode(',', array_column($data['order_subject'], 'title')) : '';
    }
}
