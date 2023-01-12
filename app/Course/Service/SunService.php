<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\SunMapper;
use Mine\Abstracts\AbstractService;

/**
 * 晒一晒服务类.
 */
class SunService extends AbstractService
{
    /**
     * @var SunMapper
     */
    public $mapper;

    public function __construct(SunMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
