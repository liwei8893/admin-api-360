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
}
