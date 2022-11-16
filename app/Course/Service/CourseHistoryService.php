<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
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
     * @return array
     */
    public function getHistoryList($data): array
    {
        return $this->mapper->getHistoryList($data);
    }
}
