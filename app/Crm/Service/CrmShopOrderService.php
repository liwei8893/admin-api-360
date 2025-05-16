<?php
declare(strict_types=1);


namespace App\Crm\Service;

use App\Crm\Mapper\CrmShopOrderMapper;
use App\Crm\Model\CrmShop;
use App\Crm\Model\CrmShopOrder;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * 订单管理服务类
 */
class CrmShopOrderService extends AbstractService
{
    /**
     * @var CrmShopOrderMapper
     */
    public $mapper;

    #[Inject]
    protected CrmUserTimelineService $userTimelineService;

    #[Inject]
    protected CrmUserCommTimelineService $userCommTimelineService;

    public function __construct(CrmShopOrderMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 新增数据.
     * @param array $data
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function save(array $data): int
    {
        $shopMod = CrmShop::query()->where('id', $data['shop_id'])->first();
        if (!$shopMod) {
            throw new NormalStatusException('商品不存在');
        }
        $data['order_number'] = snowflake_id();
        $data['created_by'] = user()->getId();
        $data['order_status'] = 8;
        // 写入用户时间线
        $createdAdminId = user()->getId();
        $createdAdminName = user()->getNickname();
        $this->userTimelineService->saveBuyShopEvent($data['user_id'], $createdAdminId, "[{$createdAdminName}]出单[{$shopMod->shop_name}]");
        return parent::save($data);
    }

    /**
     * 更新一条数据.
     * @param int $id
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    public function update(int $id, array $data): bool
    {
        return parent::update($id, $data);
    }

    /**
     * 订单审核.
     * @param int $orderId
     * @param int $status
     * @param string $comment
     * @return bool
     */
    public function auditOrder(int $orderId, int $status, string $comment = ''): bool
    {
        // 验证状态合法性
        if (!in_array($status, [7, 9])) {
            throw new NormalStatusException('审核状态必须为7(通过)或9(不通过)');
        }

        // 审核不通过时必须提供审批意见
        if ($status === 9 && empty($comment)) {
            throw new NormalStatusException('审核不通过时必须填写审批意见');
        }

        // 查询订单
        /** @var CrmShopOrder $order */
        $order = $this->mapper->read($orderId);
        if (!$order) {
            throw new NormalStatusException('订单不存在');
        }
        // 审核通过的订单如果有course课程表字段,添加课程表到crm_user_comm_timeline用户沟通时间表,规则为课程表的时间+1天
        if ($status === 7 && !empty($order->course)) {
            // 构建时间,课程表时间course_time加上1天
            $timelineData = [];
            foreach ($order->course as $item) {
                $timelineData[] = [
                    'user_id' => $order->user_id,
                    'comm_time' => date('Y-m-d H:i:s', strtotime($item['course_time']) + 86400),
                    'content' => "课程[{$item['course_name']}]回访",
                ];
            }
            $this->userCommTimelineService->batchSave($timelineData);
        }

        // 更新订单状态和审批意见
        return $this->mapper->update($orderId, ['order_status' => $status, 'audit_comment' => $comment]);
    }
}
