<?php

declare(strict_types=1);

namespace App\Order\Mapper;

use App\Order\Model\OrderTransaction;
use Mine\Abstracts\AbstractMapper;

class OrderTransactionMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = OrderTransaction::class;
    }

    /**
     * 续费表插入数据.
     * @return bool
     *              author:ZQ
     *              time:2022-08-19 14:44
     */
    public function insert(array $value): bool
    {
        return $this->model::query()->insert($value);
    }
}
