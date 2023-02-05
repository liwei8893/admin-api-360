<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\CourseHistoryMapper;
use App\Order\Model\Order;
use App\Order\Service\OrderService;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

class CourseHistoryService extends AbstractService
{
    /**
     * @var CourseHistoryMapper
     */
    #[Inject]
    public $mapper;

    #[Inject]
    protected OrderService $orderService;

    /**
     * 课程购买记录列表.
     */
    public function getHistoryList(array $data): array
    {
        return $this->mapper->getHistoryList($data);
    }

    public function batchChangeGrade(array $params): bool
    {
        foreach ($params['ids'] as $orderId) {
            /* @var Order $orderModel */
            $orderModel = $this->orderService->read($orderId);
            if (! $orderModel) {
                continue;
            }
            if ($params['type'] === 1) {
                // 增加对应年级
                $orderModel->orderGrade()->attach($params['grade']);
            } elseif ($params['type'] === 2) {
                // 去掉对应年级
                $orderModel->orderGrade()->detach($params['grade']);
            } elseif ($params['type'] === 0) {
                // 同步年级
                $orderModel->orderGrade()->sync($params['grade']);
            }
        }
        return true;
    }
}
