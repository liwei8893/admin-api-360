<?php

declare(strict_types=1);

namespace App\Pay\Service;

use App\Pay\Mapper\PayConfigMapper;
use Mine\Abstracts\AbstractService;

/**
 * 商户配置服务类.
 */
class PayConfigService extends AbstractService
{
    /**
     * @var PayConfigMapper
     */
    public $mapper;

    public function __construct(PayConfigMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
