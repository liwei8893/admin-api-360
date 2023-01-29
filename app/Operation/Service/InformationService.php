<?php

declare(strict_types=1);

namespace App\Operation\Service;

use App\Operation\Mapper\InformationMapper;
use Mine\Abstracts\AbstractService;

/**
 * 资讯列表服务类.
 */
class InformationService extends AbstractService
{
    /**
     * @var InformationMapper
     */
    public $mapper;

    public function __construct(InformationMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        $data = $this->handleData($data);
        return $this->mapper->save($data);
    }

    protected function handleData(array $data): array
    {
        $data['created_id'] = user()->getId();
        return $data;
    }
}
