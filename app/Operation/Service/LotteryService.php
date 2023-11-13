<?php

declare(strict_types=1);

namespace App\Operation\Service;

use App\Operation\Mapper\LotteryMapper;
use Mine\Abstracts\AbstractService;

/**
 * 抽奖管理服务类.
 */
class LotteryService extends AbstractService
{
    /**
     * @var LotteryMapper
     */
    public $mapper;

    public function __construct(LotteryMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        $data['created_by'] = user()->getId();
        $data['updated_by'] = user()->getId();
        return parent::save($data);
    }

    /**
     * 更新一条数据.
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_by'] = user()->getId();
        return parent::update($id, $data);
    }

    public function getNowDateId(): null|int
    {
        return $this->mapper->getNowDateId();
    }
}
