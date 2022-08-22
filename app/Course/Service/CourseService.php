<?php

namespace App\Course\Service;

use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

class CourseService extends AbstractService
{
    /**
     * @var \App\Course\Mapper\CourseMapper
     */
    #[Inject]
    public $mapper;

}