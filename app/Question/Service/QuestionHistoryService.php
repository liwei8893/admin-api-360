<?php

declare(strict_types=1);

namespace App\Question\Service;

use App\Question\Mapper\QuestionHistoryMapper;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractService;

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
}
