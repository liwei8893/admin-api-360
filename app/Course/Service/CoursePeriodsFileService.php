<?php
declare(strict_types=1);


namespace App\Course\Service;

use App\Course\Mapper\CoursePeriodsFileMapper;
use Mine\Abstracts\AbstractService;

/**
 * 章节文件服务类
 */
class CoursePeriodsFileService extends AbstractService
{
    /**
     * @var CoursePeriodsFileMapper
     */
    public $mapper;

    public function __construct(CoursePeriodsFileMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
