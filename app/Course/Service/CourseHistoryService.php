<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\CourseHistoryMapper;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

class CourseHistoryService extends AbstractService
{
    /**
     * @var CourseHistoryMapper
     */
    #[Inject]
    public $mapper;

    /**
     * 课程购买记录列表.
     * @param $data
     */
    public function getHistoryList($data): array
    {
        return $this->mapper->getHistoryList($data);
    }
}
