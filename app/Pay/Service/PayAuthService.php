<?php

declare(strict_types=1);

namespace App\Pay\Service;

use App\Pay\Mapper\PayAuthMapper;
use Mine\Abstracts\AbstractService;

/**
 * 公众号配置服务类.
 */
class PayAuthService extends AbstractService
{
    /**
     * @var PayAuthMapper
     */
    public $mapper;

    public function __construct(PayAuthMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
