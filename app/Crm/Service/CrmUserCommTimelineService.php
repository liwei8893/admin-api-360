<?php
declare(strict_types=1);

namespace App\Crm\Service;

use App\Crm\Mapper\CrmUserCommTimelineMapper;
use Carbon\Carbon;
use Mine\Abstracts\AbstractService;

/**
 * 用户沟通时间服务类
 */
class CrmUserCommTimelineService extends AbstractService
{
    /**
     * @var CrmUserCommTimelineMapper
     */
    public $mapper;

    public function __construct(CrmUserCommTimelineMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    // 注册时初始化沟通时间,一周1次,一共3次
    public function initCommTimeline(int $userId): bool
    {
        // 获取当前时间
        $nowDate = Carbon::now();
        // comm_time时间每周递增
        $insetData = [
            ['user_id' => $userId, 'comm_time' => $nowDate->toDateString(), 'content' => '第一次沟通'],
            ['user_id' => $userId, 'comm_time' => $nowDate->addWeek()->toDateString(), 'content' => '第二次沟通'],
            ['user_id' => $userId, 'comm_time' => $nowDate->addWeeks(2)->toDateString(), 'content' => '第三次沟通'],
            ['user_id' => $userId, 'comm_time' => $nowDate->addWeeks(3)->toDateString(), 'content' => '第四次沟通'],
        ];
        return $this->batchSave($insetData);
    }
}
