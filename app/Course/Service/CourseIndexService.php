<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\CourseIndexMapper;
use Mine\Abstracts\AbstractService;

/**
 * 体验课管理服务类.
 */
class CourseIndexService extends AbstractService
{
    /**
     * @var CourseIndexMapper
     */
    public $mapper;

    public function __construct(CourseIndexMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
