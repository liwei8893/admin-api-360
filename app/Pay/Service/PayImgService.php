<?php

declare(strict_types=1);

namespace App\Pay\Service;

use App\Pay\Mapper\PayImgMapper;
use Mine\Abstracts\AbstractService;

/**
 * 图片配置服务类.
 */
class PayImgService extends AbstractService
{
    /**
     * @var PayImgMapper
     */
    public $mapper;

    public function __construct(PayImgMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
