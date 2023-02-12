<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Order\Mapper\UsersRenewMapper;
use App\Order\Model\UsersRenew;
use App\Score\Event\ScoreAddEvent;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
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
                    // 如果为续费,叠加订单金额
                    $order->actual_price += $model->money * 100;
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
}
