<?php

namespace App\Course\Mapper;

use App\Course\Model\CourseBasis;
use Mine\Abstracts\AbstractMapper;

class CourseMapper extends AbstractMapper
{

    public function assignModel():void
    {
        $this->model = CourseBasis::class;
    }
}