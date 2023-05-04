<?php

declare(strict_types=1);

namespace App\Pay\Service;

use App\Pay\Mapper\PayLinkMapper;
use Mine\Abstracts\AbstractService;

/**
 * 付款链接服务类.
 */
class PayLinkService extends AbstractService
{
    /**
     * @var PayLinkMapper
     */
    public $mapper;

    public function __construct(PayLinkMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
