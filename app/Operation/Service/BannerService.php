<?php

declare(strict_types=1);

namespace App\Operation\Service;

use App\Operation\Mapper\BannerMapper;
use Mine\Abstracts\AbstractService;

/**
 * 轮播管理服务类.
 */
class BannerService extends AbstractService
{
    /**
     * @var BannerMapper
     */
    public $mapper;

    public function __construct(BannerMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
