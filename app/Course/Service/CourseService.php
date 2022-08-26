<?php

namespace App\Course\Service;

use Hyperf\Database\Model\Collection;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

class CourseService extends AbstractService
{
    /**
     * @var \App\Course\Mapper\CourseMapper
     */
    #[Inject]
    public $mapper;

    /**
     * @param array $ids
     * @param array $select
     * @return \Hyperf\Database\Model\Collection|array
     * author:ZQ
     * time:2022-08-26 15:38
     */
    public function getCourseInfoByIds(array $ids, array $select = []): Collection|array
    {
        return $this->mapper->getCourseInfoByIds($ids, $select = []);
    }
}