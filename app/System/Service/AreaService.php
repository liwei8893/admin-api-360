<?php

declare(strict_types=1);

namespace App\System\Service;

use App\System\Mapper\AreaMapper;
use Mine\Abstracts\AbstractService;

/**
 * 区域字典服务类.
 */
class AreaService extends AbstractService
{
    /**
     * @var AreaMapper
     */
    public $mapper;

    public function __construct(AreaMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
