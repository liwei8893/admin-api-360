<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Order\Model\Order;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Helper\LoginUser;

class UsersRenewService extends AbstractService
{
    /**
     * @var \App\Order\Mapper\UsersRenewMapper
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
    public function recordUserRenew($data): bool
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
            'audit_status' => $this->loginUser->isNoAuditRole() ? Order::AUDIT_SUCCESS : Order::AUDIT_PENDING,
            'remark' => $data['remark'] ?? '',
        ];
        return $this->mapper->insert($params);
    }
}
