<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\CourseSignupConfigMapper;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

/**
 * 课程报名配置表服务类.
 */
class CourseSignupConfigService extends AbstractService
{
    /**
     * @var CourseSignupConfigMapper
     */
    #[Inject]
    public $mapper;
}
