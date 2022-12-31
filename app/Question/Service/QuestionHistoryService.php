<?php

declare(strict_types=1);

namespace App\Question\Service;

use App\Question\Mapper\QuestionHistoryMapper;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;

/**
 * 错题表服务类.
 */
class QuestionHistoryService extends AbstractService
{
    /**
     * @var QuestionHistoryMapper
     */
    public $mapper;

    public function __construct(QuestionHistoryMapper $mapper)
    {
        $this->mapper = $mapper;
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
            ->keyBy(fn ($item) => 'month' . $item['month']);
        $total = $this->mapper->getReportByTotal();
        $rate = $this->mapper->getRankingRate();
        return [
            'chart' => $monthMap->merge($data)->values()->toArray(),
            'total' => $total,
            'rate' => $rate,
        ];
    }

    public function changeErrorCollect(array $params): bool
    {
        $userId = user('app')->getId();
        $model = $this->mapper->first(['user_id' => $userId, 'id' => $params['id']]);
        if (! $model) {
            throw new NormalStatusException('只能收藏自己的题目!');
        }
        $is_collect = $model['is_collect'] === 1 ? 0 : 1;
        return $this->update($params['id'], ['is_collect' => $is_collect]);
    }
}
