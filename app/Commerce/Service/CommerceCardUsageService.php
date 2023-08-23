<?php

declare(strict_types=1);

namespace App\Commerce\Service;

use App\Commerce\Mapper\CommerceCardUsageMapper;
use Mine\Abstracts\AbstractService;

/**
 * 电商卡使用记录服务类.
 */
class CommerceCardUsageService extends AbstractService
{
    /**
     * @var CommerceCardUsageMapper
     */
    public $mapper;

    public function __construct(CommerceCardUsageMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
