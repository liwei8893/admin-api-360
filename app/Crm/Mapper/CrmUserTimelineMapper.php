<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\Crm\Mapper;

use App\Crm\Model\CrmUserTimeline;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 用户时间线记录表Mapper类
 */
class CrmUserTimelineMapper extends AbstractMapper
{
    /**
     * @var CrmUserTimeline
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CrmUserTimeline::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        // 用户ID
        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }

        // 创建人id
        if (isset($params['created_by']) && $params['created_by'] !== '') {
            $query->where('created_by', '=', $params['created_by']);
        }

        // 事件
        if (isset($params['event']) && $params['event'] !== '') {
            $query->where('event', 'like', '%' . $params['event'] . '%');
        }

        // 事件详情
        if (isset($params['event_detail']) && $params['event_detail'] !== '') {
            $query->where('event_detail', 'like', '%' . $params['event_detail'] . '%');
        }

        // 创建时间
        if (isset($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0], $params['created_at'][1]]
            );
        }

        if (!empty($params['withAdmin'])) {
            $query->with(['admin:id,nickname']);
        }

        return $query;
    }
}
