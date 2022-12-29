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

    public function getReport(): array
    {
        $monthMap = collect([
            'month01' => ['month' => '01', 'num' => 0],
            'month02' => ['month' => '02', 'num' => 0],
            'month03' => ['month' => '03', 'num' => 0],
            'month04' => ['month' => '04', 'num' => 0],
            'month05' => ['month' => '05', 'num' => 0],
            'month06' => ['month' => '06', 'num' => 0],
            'month07' => ['month' => '07', 'num' => 0],
            'month08' => ['month' => '08', 'num' => 0],
            'month09' => ['month' => '09', 'num' => 0],
            'month10' => ['month' => '10', 'num' => 0],
            'month11' => ['month' => '11', 'num' => 0],
            'month12' => ['month' => '12', 'num' => 0],
        ]);
        $data = $this->mapper->getReportByMonth()
            ->makeHidden(['timeRate'])
            ->keyBy(fn ($item) => 'month' . $item['month']);
        $total = $this->mapper->getReportByTotal();
        $rate = $this->mapper->getRankingRate();
        return [
            'chart' => $monthMap->merge($data)->values()->toArray(),
            'total' => $total,
            'rate' => $rate,
        ];
    }

    public function getUserRecord(): Collection|array
    {
        $userId = user('app')->getId();
        $recordModel = $this->mapper->getRecordByUserId($userId);
        return $recordModel->each(static function ($item) {
            $item['courseBasisId'] = $item['courseBasis']['course_basis_id'] ?? null;
            $item['courseBasisTitle'] = $item['courseBasis']['title'] ?? '';
            $item['coursePeriodTitle'] = $item['coursePeriod']['title'] ?? '';
            $item['timeRate'] = round($item['watch_time'] / $item['video_duration'] * 100, 2);
            return $item;
        })->groupBy('courseBasisId');
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
