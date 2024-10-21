<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\Score\Event\ScoreAddEvent;
use App\Users\Mapper\UserCourseRecordMapper;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Database\Model\Collection;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use function Hyperf\Collection\collect;

/**
 * 听课记录服务类.
 */
class UserCourseRecordService extends AbstractService
{
    /**
     * @var UserCourseRecordMapper
     */
    public $mapper;

    #[Inject]
    protected UserScoreService $userScoreService;


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
        if (!$recordModel) {
            return [];
        }
        $coursePeriodModel = $recordModel->coursePeriod()
            ->with(['courseBasis:id,course_title'])
            ->select(['id', 'title', 'course_basis_id', 'qiniu_url'])
            ->first();
        if (!$coursePeriodModel) {
            return [];
        }
        return $coursePeriodModel->toArray();
    }

    /**
     * 获取听课排行榜,缓存1小时.
     */
    #[Cacheable(prefix: 'ranking', value: 'course', ttl: 86400)]
    public function getRanking(): array
    {
        return $this->mapper->getRanking()->map(function ($item) {
            if (!empty($item['users'])) {
                if ($item['users']['mobile'] === $item['users']['user_name']) {
                    $item['users']['user_name'] = substr_replace($item['users']['user_name'], '****', 3, 4);
                }
                unset($item['users']['mobile']);
            }
            return $item;
        })->toArray();
    }

    public function getRankingCustomDate(array $params): Collection|array
    {
        return $this->mapper->getRanking($params)->map(function ($item) {
            if (!empty($item['users'])) {
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

    public function getRankingMeCustomDate(int $userId, array $params): array
    {
        return ['ranking' => $this->mapper->getRankingMe($userId, $params)];
    }

    /**
     * 获取听课节数报告,缓存24小时.
     */
    #[Cacheable(prefix: 'report', value: 'course_#{userId}', ttl: 86400)]
    public function getReport(int $userId): array
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
            ->keyBy(fn($item) => 'month' . $item['month']);
        $total = $this->mapper->getReportByTotal($userId);
        $rate = $this->mapper->getRankingRate($userId);
        return [
            'chart' => $monthMap->merge($data)->values()->toArray(),
            'total' => $total,
            'rate' => $rate,
        ];
    }

    public function getUserRecord(): array|Collection
    {
        $userId = user('app')->getId();
        $recordModel = $this->mapper->getRecordByUserId($userId);
        return $recordModel->each(static function ($item) {
            $item['courseBasisId'] = $item['courseBasis']['course_basis_id'] ?? null;
            $item['courseBasisTitle'] = $item['courseBasis']['title'] ?? '';
            $item['coursePeriodTitle'] = $item['coursePeriod']['title'] ?? '';
            $item['timeRate'] = $item['video_duration'] * 100 !== 0 ? round($item['watch_time'] / $item['video_duration'] * 100, 2) : 0.0;
            return $item;
        })->groupBy('courseBasisId');
    }

    public function getUserRecordPageList(array $params): array
    {
        $params['userId'] = user('app')->getId();
        $pageData = $this->mapper->getRecordPageList($params);
        foreach ($pageData['items'] as &$item) {
            $item['courseBasisId'] = $item['courseBasis']['course_basis_id'] ?? null;
            $item['courseBasisTitle'] = $item['courseBasis']['title'] ?? '';
            $item['coursePeriodTitle'] = $item['coursePeriod']['title'] ?? '';
            $item['timeRate'] = $item['video_duration'] * 100 !== 0 ? round($item['watch_time'] / $item['video_duration'] * 100, 2) : 0.0;
        }
        return $pageData;
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
        if (!$setRecordState || !$setRecordTodayState) {
            throw new NormalStatusException('保存播放记录失败!');
        }
        // Todo 添加听课积分事件
        event(new ScoreAddEvent('course', $params['userId'], 0));
        return true;
    }

    /**
     * 获取未使用的番茄数量
     * @return array
     */
    public function getUnusedTomato(): array
    {
        $userId = user('app')->getId();
        if (!$userId) {
            throw new NormalStatusException('请先登录!');
        }
        return ['tomato' => $this->mapper->getUnusedTomatoCount($userId)];
    }

    /**
     * 使用番茄
     * @return int
     */
    #[Transaction]
    public function usedTomato(): int
    {
        $userId = user('app')->getId();
        if (!$userId) {
            throw new NormalStatusException('未查询到用户!');
        }
        $recordModel = $this->mapper->getUnusedTomatoFirst($userId);
        if (!$recordModel) {
            throw new NormalStatusException('数量不足!');
        }
        // 把记录改为已使用
        $recordModel->complete_status = 2;
        $recordModel->save();
        // 增加需求,完课获得的积分在1-10分之间
        $score = mt_rand(1, 10);
        // 增加积分
        $this->userScoreService->changeScore([
            'user_id' => $recordModel->user_id,
            'origin_id' => $recordModel->id,
            'channel' => '完课获得',
            'channel_type' => 4,
            'score' => $score,
            'type' => 1,
        ]);
        return $score;
    }

    protected function handleExportData(array &$data): void
    {
        $data['watch_time'] = round($data['watch_time'] / 60) . '分钟';
        $data['video_duration'] = round($data['video_duration'] / 60) . '分钟';
        $data['timeRate'] .= '%';
        $data['created_at'] = date('Y-m-d H:i:s', (int)$data['created_at']);
        $data['updated_at'] = date('Y-m-d H:i:s', (int)$data['updated_at']);
    }
}
