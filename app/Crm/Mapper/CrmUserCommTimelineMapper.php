<?php
declare(strict_types=1);

namespace App\Crm\Mapper;

use App\Crm\Model\CrmUserCommTimeline;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 用户沟通时间Mapper类
 */
class CrmUserCommTimelineMapper extends AbstractMapper
{
    /**
     * @var CrmUserCommTimeline
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CrmUserCommTimeline::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        // 主键ID
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        // 用户ID
        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }

        // 沟通时间
        if (isset($params['comm_time']) && is_array($params['comm_time']) && count($params['comm_time']) === 2) {
            $query->whereBetween(
                'comm_time',
                [$params['comm_time'][0], $params['comm_time'][1]]
            );
        }

        // 沟通内容摘要
        if (isset($params['content']) && $params['content'] !== '') {
            $query->where('content', 'like', '%' . $params['content'] . '%');
        }

        return $query;
    }
}
