<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Order\Mapper\OrderSummaryMapper;
use Mine\Abstracts\AbstractService;

/**
 * 核单记录服务类.
 */
class OrderSummaryService extends AbstractService
{
    /**
     * @var OrderSummaryMapper
     */
    public $mapper;

    public function __construct(OrderSummaryMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function setSummaryAdmin(array $params): bool
    {
        foreach ($params['orderIds'] as $orderId) {
            $this->mapper->setSummaryAdmin(['orderId' => $orderId, 'adminId' => $params['adminId']]);
        }
        return true;
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        $data['created_id'] = user()->getId();
        if (isset($data['created_at'])) {
            unset($data['created_at']);
        }
        return parent::save($data);
    }

    /**
     * 需要处理导出数据时,重写函数.
     */
    protected function handleExportData(array &$data): void
    {
        $levelMap = ['未知', 'A类', 'B类', 'C类'];
        $statusMap = ['未完成', '已完成'];
        $typeMap = ['电话核单', '微信核单'];
        $data['type'] = $typeMap[$data['type'] - 1];
        $data['level'] = $levelMap[$data['level']] ?? $levelMap[0];
        $data['status'] = $statusMap[$data['status']] ?? $statusMap[0];
        $data['has_wechat'] = $data['has_wechat'] === 1 ? '是' : '否';
        $data['has_connect'] = $data['has_connect'] === 1 ? '是' : '否';
        $data['created_at'] = date('Y-m-d H:i:s', (int) $data['created_at']);
        $data['updated_at'] = date('Y-m-d H:i:s', (int) $data['updated_at']);
    }
}
