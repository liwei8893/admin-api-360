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

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        $data['created_id'] = user()->getId();
        $data['created_name'] = user()->getNickname();
        if (isset($data['created_at'])) {
            unset($data['created_at']);
        }
        return parent::save($data);
    }
}
