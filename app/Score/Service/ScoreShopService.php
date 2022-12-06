<?php

declare(strict_types=1);

namespace App\Score\Service;

use App\Score\Mapper\ScoreShopMapper;
use Mine\Abstracts\AbstractService;

/**
 * 积分管理服务类.
 */
class ScoreShopService extends AbstractService
{
    /**
     * @var ScoreShopMapper
     */
    public $mapper;

    public function __construct(ScoreShopMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
