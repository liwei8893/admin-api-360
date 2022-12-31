<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\CoursePeriodMapper;
use Hyperf\Database\Model\Collection;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

class CoursePeriodService extends AbstractService
{
    /**
     * @var CoursePeriodMapper
     */
    #[Inject]
    public $mapper;

    public function getPlanMonth($params): Collection|array
    {
        // 处理季节 1,2月对应寒假2节课 3,4,5,6月对应春季4节课 7,8月对应暑假4节课， 9,10,11,12月对应秋季4节课
        $seasonMap = ['', 4, 4, 1, 1, 1, 1, 2, 2, 3, 3, 3, 3];
        $params['season'] = $seasonMap[$params['month'] ?? date('n')];
        // 处理limit
        $params['limit'] = $params['season'] === 4 ? 2 : 4;
        // 处理offset
        $offsetMap = [0, 0, 2, 0, 4, 8, 12, 0, 4, 0, 4, 8, 12];
        $params['offset'] = $offsetMap[$params['month']];
        // 科目默认语文
        $params['subject_id'] = $params['subject_id'] ?? 3;
        return $this->mapper->getPlanMonth($params);
    }
}
