<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\Score\Event\ScoreAddEvent;
use App\Users\Mapper\UserCourseRecordMapper;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
     * 获取听课排行榜,缓存1小时.
     */
    #[Cacheable(prefix: 'ranking', value: 'course', ttl: 86400)]
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

    /**
     * 获取用户听课排名,缓存1小时.
     */
    #[Cacheable(prefix: 'ranking', value: 'courseMe_#{userId}', ttl: 86400)]
    public function getRankingMe(int $userId): array
    {
        return ['ranking' => $this->mapper->getRankingMe($userId)];
    }

    /**
     * 获取听课节数报告,缓存24小时.
     */
    #[Cacheable(prefix: 'report', value: 'course_#{userId}', ttl: 86400)]
    public function getReport(int $userId): array
    {
        $monthMap = \Hyperf\Collection\collect([
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
        $total = $this->mapper->getReportByTotal($userId);
        $rate = $this->mapper->getRankingRate($userId);
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

    /**
     * 记录听课时间.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Transaction]
    public function setWatchTime(array $params): bool
    {
        // 视频总时间
        $params['videoDuration'] = $params['videoDuration'] ?? 0;
        // 听课时间
        $params['watchTime'] = $params['watchTime'] ?? 120;
        $params['userId'] = user('app')->getId();
        $setRecordState = $this->mapper->setWatchTime($params);
        $setRecordTodayState = $this->mapper->setWatchTimeToday($params);
        if (! $setRecordState || ! $setRecordTodayState) {
            throw new NormalStatusException('保存播放记录失败!');
        }
        // Todo 添加听课积分事件
        event(new ScoreAddEvent('course', $params['userId'], 0));
        return true;
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
