<?php

declare(strict_types=1);

namespace App\Sta\Service;

use App\Sta\Mapper\LearningReportMapper;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

class LearningReportService extends AbstractService
{
    /**
     * @var LearningReportMapper
     */
    #[Inject]
    public $mapper;

    /**
     * 上周学习报告.
     */
    public function getReportByWeek(array $params): array
    {
        // 获取上周开始时间,结束时间
        $params['start_time'] = Carbon::now()->subWeek()->startOfWeek()->timestamp;
        //        $params['start_time'] = Carbon::parse('2023-03-01')->timestamp;
        $params['end_time'] = Carbon::now()->subWeek()->endOfWeek()->timestamp;
        //        $params['end_time'] = Carbon::now()->timestamp;
        $params['user_id'] = user('app')->getId();
        // 新学习了几节课
        $courseRecord = $this->mapper->getLearningCourseCount($params);
        $courseCount = $courseRecord['count'];
        // 实际学习时长/分钟
        $learningTime = $courseRecord['minute'];
        // 做题数,分科,语,数,英
        $questionCount = $this->mapper->getQuestionOfSubjectCount($params);
        // 客观题正确率,客观题总数,客观题正确数
        $questionRate = $this->mapper->getQuestionObjectiveRate($params);
        return [
            'courseCount' => $courseCount,
            'learningTime' => $learningTime,
            'questionCount' => $questionCount,
            'questionRate' => $questionRate,
        ];
    }
}
