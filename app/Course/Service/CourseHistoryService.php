<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\CourseHistoryMapper;
use App\Order\Model\Order;
use App\Order\Service\OrderService;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\MineCollection;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

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
            if (!$orderModel) {
                continue;
            }
            if ($params['type'] === 1) {
                // 增加对应年级
                $orderModel->orderGrade()->syncWithoutDetaching($params['grade']);
                $orderModel->orderSubject()->syncWithoutDetaching($params['subject']);
            } elseif ($params['type'] === 2) {
                // 去掉对应年级
                $orderModel->orderGrade()->detach($params['grade']);
                $orderModel->orderSubject()->detach($params['subject']);
            } elseif ($params['type'] === 0) {
                // 同步年级
                $orderModel->orderGrade()->sync($params['grade']);
                $orderModel->orderSubject()->sync($params['subject']);
            }
        }
        return true;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function courseHistoryExport(array $params, string $dto, string $filename): ResponseInterface
    {
        $params['pageSize'] = 30000;
        $data = $this->getHistoryList($params);
        $cb = function ($item) {
            $item['orderGrade'] = $item['orderGrade']->implode('title', ',');
            $item['orderSubject'] = $item['orderSubject']->implode('title', ',');
            $item['createdAt'] = $item['created_at']->toDateTimeString();
            return $item->toArray();
        };
        return (new MineCollection())->export($dto, $filename, $data['items'], $cb);
    }
}
