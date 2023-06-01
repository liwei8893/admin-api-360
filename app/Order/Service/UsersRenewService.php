<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Order\Mapper\UsersRenewMapper;
use App\Order\Model\UsersRenew;
use App\Score\Event\ScoreAddEvent;
use App\Users\Service\UserScoreService;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;
use Mine\Helper\LoginUser;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UsersRenewService extends AbstractService
{
    /**
     * @var UsersRenewMapper
     */
    #[inject]
    public $mapper;

    #[Inject]
    protected LoginUser $loginUser;

    #[Inject]
    protected UserScoreService $userScoreService;

    /**
     * 写入续费表
     * author:ZQ
     * time:2022-08-19 14:34.
     * @param mixed $data
     */
    public function recordUserRenew(array $data): int
    {
        $params = [
            'order_id' => $data['id'],
            'indate_start' => $data['startDate'],
            'indate_end' => $data['endDate'],
            'status' => $data['status'],
            'shop_id' => $data['shop_id'],
            'user_id' => $data['user_id'],
            'created_at' => time(),
            'created_id' => $this->loginUser->getId(),
            'created_name' => $this->loginUser->getUsername(),
            'money' => $data['money'] ?? 0,
            'audit_status' => $this->loginUser->isNoAuditRole() ? UsersRenew::AUDIT_SUCCESS : UsersRenew::AUDIT_PENDING,
            'remark' => $data['remark'] ?? '',
            'real_year' => $data['real_year'] ?? '',
        ];
        return $this->mapper->insert($params);
    }

    /**
     * 修改有效期待审核列表.
     */
    public function renewList(array $data): array
    {
        $data['withUsers'] = true;
        $data['withCourse'] = true;
        $data['orderBy'] = ['id'];
        $data['orderType'] = ['desc'];
        $data['audit_status'] = $data['audit_status'] ?? UsersRenew::AUDIT_PENDING;
        return $this->mapper->getPageList($data);
    }

    /**
     * 续费审核.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Transaction]
    public function auditRenew(array $params): bool
    {
        foreach ($params['ids'] as $id) {
            /* @var UsersRenew $model */
            $model = $this->mapper->read($id);
            if (! $model) {
                continue;
            }
            // 查询订单
            $order = $model->order;
            if (! $order) {
                continue;
            }
            // 状态不为待审核跳过
            if ($model->audit_status !== UsersRenew::AUDIT_PENDING) {
                continue;
            }
            // 审核通过
            if ($params['audit_status'] === UsersRenew::AUDIT_SUCCESS) {
                $model->audit_status = UsersRenew::AUDIT_SUCCESS;
                // 同步订单状态
                if ($model->status === 1) {
                    $order->is_renew = $model->status;
                }
                // 叠加有效期
                $order->indate += $model->renew_day;
                $order->save();
            } else {
                // 审核不通过
                $model->audit_status = UsersRenew::AUDIT_REJECT;
            }
            $model->cause_text = $params['cause_text'] ?? '';
            $model->save();
            // TODO 增加积分 renew 续费会员时,续费会员&是续费不是修改有效期&审核通过
            if ($order->shop_id === 950 && $model->status === 1 && $params['audit_status'] === UsersRenew::AUDIT_SUCCESS) {
                event(new ScoreAddEvent('renew', $order->user_id, $model->id));
            }
        }
        return true;
    }

    /**
     * 编辑续费订单价格,实际年数.
     */
    #[Transaction]
    public function editRenew(array $params): bool
    {
        /** @var UsersRenew $renewModel */
        $renewModel = $this->mapper->read($params['id']);
        if (! $renewModel) {
            throw new NormalStatusException('订单不存在');
        }
        // 不是续费单直接跳过
        if ($renewModel->status === UsersRenew::STATUS_CHANGE) {
            return true;
        }
        $renewMoney = (int) $renewModel->money;
        $newMoney = (int) $params['money'];
        // 修改续费金额
        if ($newMoney !== $renewMoney) {
            // 要变更的积分
            $diffScore = $newMoney - $renewMoney;
            $this->userScoreService->changeScore([
                'user_id' => $renewModel->user_id,
                'origin_id' => $renewModel->id,
                'channel' => '管理员操作',
                'channel_type' => 0,
                'score' => abs($diffScore),
                'type' => $diffScore > 0 ? 1 : 0,
            ]);
            // 修改金额
            $renewModel->money = $newMoney;
        }
        $renewModel->real_year = $params['real_year'];
        return $renewModel->save();
    }
}
