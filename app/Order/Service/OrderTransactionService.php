<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Order\Mapper\OrderTransactionMapper;
use App\Order\Mapper\UsersRenewMapper;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Helper\LoginUser;

class OrderTransactionService extends AbstractService
{
    /**
     * @var OrderTransactionMapper
     */
    #[inject]
    public $mapper;

    #[Inject]
    protected LoginUser $loginUser;

    #[Inject]
    protected UsersRenewMapper $usersRenewMapper;

    /**
     * 转人记录
     * author:ZQ
     * time:2022-08-19 14:34.
     * @param mixed $data
     */
    public function OrderToUserRecord($data): bool
    {
        $basisParams = [
            'order_id' => $data['newOrderId'],
            'object_id' => $data['oldOrderId'],
            'type_id' => 3,
            'type_name' => '转人',
            'remark' => $data['remark'],
            'create_at' => date('Y-m-d H:i:s'),
            'operator_id' => $this->loginUser->getId(),
        ];
        $oldParams = [
            'user_id' => $data['oldUserId'],
            'new_user_id' => $data['newUserId'],
        ];
        $newParams = [
            'user_id' => $data['newUserId'],
            'new_user_id' => $data['oldUserId'],
        ];
        // 老订单续费记录用户id换成新用户id
        $this->usersRenewMapper->transformUser($data['oldUserId'], $data['oldOrderId'], $data['newUserId'], $data['newOrderId']);
        // 转人插入两条记录,新老用户各一条
        $statusOld = $this->mapper->insert(array_merge($basisParams, $oldParams));
        $statusNew = $this->mapper->insert(array_merge($basisParams, $newParams));
        return $statusOld && $statusNew;
    }

    /**
     * 转班.
     * @param mixed $data
     * @return bool
     *              author:ZQ
     *              time:2022-08-20 11:31
     */
    public function OrderToCourseRecord($data): bool
    {
        $params = [
            'new_shop_id' => $data['newShopId'],
            'object_id' => $data['oldShopId'],
            'type_id' => 2,
            'type_name' => '转班',
            'order_id' => $data['order_id'],
            'user_id' => $data['user_id'],
            'money' => $data['money'],
            'remark' => $data['remark'],
            'create_at' => date('Y-m-d H:i:s'),
            'operator_id' => $this->loginUser->getId(),
        ];
        return $this->mapper->insert($params);
    }

    public function OrderToRefundRecord($data): bool
    {
        $params = [
            'type_id' => 1,
            'type_name' => '退费',
            'order_id' => $data['order_id'],
            'user_id' => $data['user_id'],
            'money' => $data['money'],
            'remark' => $data['remark'],
            'create_at' => date('Y-m-d H:i:s'),
            'operator_id' => $this->loginUser->getId(),
        ];
        return $this->mapper->insert($params);
    }
}
