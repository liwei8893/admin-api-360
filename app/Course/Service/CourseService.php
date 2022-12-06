<?php

declare(strict_types=1);

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
     * @return array|\Hyperf\Database\Model\Collection
     *                                                 author:ZQ
     *                                                 time:2022-08-26 15:38
     */
    public function getCourseInfoByIds(array $ids, array $select = []): Collection|array
    {
        return $this->mapper->getCourseInfoByIds($ids, $select = []);
    }
}
