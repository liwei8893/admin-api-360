<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\Users\Mapper\UserCourseRecordMapper;
use Mine\Abstracts\AbstractService;

/**
 * 听课记录服务类.
 */
class UserCourseRecordService extends AbstractService
{
    /**
     * @var UserCourseRecordMapper
     */
    public $mapper;

    public function __construct(UserCourseRecordMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 获取最后一次观看课程记录.
     * @return array
     */
    public function lastRecord(): array
    {
        $userId = user()->getId();
        $recordModel = $this->mapper->lastRecord($userId);
        if (! $recordModel) {
            return [];
        }
        return $recordModel->coursePeriod()
            ->with(['courseBasis:id,course_title'])
            ->select(['id', 'title', 'course_basis_id', 'qiniu_url'])
            ->first()
            ->toArray();
    }

    protected function handleExportData(array &$data): void
    {
        foreach ($data as &$item) {
            $item['watch_time'] = round($item['watch_time'] / 60) . '分钟';
            $item['video_duration'] = round($item['video_duration'] / 60) . '分钟';
            $item['timeRate'] .= '%';
        }
    }
}
