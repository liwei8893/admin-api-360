<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\Users\Mapper\UserCourseRecordMapper;
use Hyperf\Database\Model\Collection;
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

    /**
     * 获取听课排行榜.
     */
    public function getRanking(): Collection|array
    {
        return $this->mapper->getRanking()->map(function ($item) {
            if (! empty($item['users'])) {
                if ($item['users']['mobile'] === $item['users']['user_name']) {
                    $item['users']['user_name'] = substr_replace($item['users']['user_name'], '****', 3, 4);
                }
                unset($item['users']['mobile']);
            }
            return $item;
        });
    }

    public function getRankingMe(): array
    {
        return ['ranking' => $this->mapper->getRankingMe()];
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
