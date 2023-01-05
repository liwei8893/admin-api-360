<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\CourseMapper;
use Hyperf\Database\Model\Collection;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

class CourseService extends AbstractService
{
    /**
     * @var CourseMapper
     */
    #[Inject]
    public $mapper;

    public function getCourseInfoByIds(array $ids, array $select = []): Collection|array
    {
        return $this->mapper->getCourseInfoByIds($ids, $select);
    }
}
