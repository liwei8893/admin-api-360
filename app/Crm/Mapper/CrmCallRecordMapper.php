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

use App\Crm\Model\CrmCallRecord;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 话单记录Mapper类
 */
class CrmCallRecordMapper extends AbstractMapper
{
    /**
     * @var CrmCallRecord
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CrmCallRecord::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        // 坐席号码，仅API自动外呼有此参数
        if (isset($params['caller']) && $params['caller'] !== '') {
            $query->where('caller', 'like', '%' . $params['caller'] . '%');
        }

        // 被叫号码
        if (isset($params['callee']) && $params['callee'] !== '') {
            $query->where('callee', 'like', '%' . $params['callee'] . '%');
        }

        // 自动外呼任务ID，仅API自动外呼有此参数
        if (isset($params['task_id']) && $params['task_id'] !== '') {
            $query->where('task_id', 'like', '%' . $params['task_id'] . '%');
        }

        // 状态码，1为呼叫成功，0为呼叫失败,2为呼叫中
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', '=', $params['status']);
        }

        // 挂断方信息、呼叫状态信息和SIP响应状态码，中间用英文逗号隔开，辅助排查故障
        if (isset($params['status_info']) && $params['status_info'] !== '') {
            $query->where('status_info', 'like', '%' . $params['status_info'] . '%');
        }

        // 通话时长，大于等于0的整数，单位为秒
        if (isset($params['duration']) && $params['duration'] !== '') {
            $query->where('duration', '=', $params['duration']);
        }

        // 通话唯一标识。
        if (isset($params['return_uuid']) && $params['return_uuid'] !== '') {
            $query->where('return_uuid', 'like', '%' . $params['return_uuid'] . '%');
        }

        // 录音地址，记录到CRM系统的通话记录，点击可以播放。呼叫失败则为空
        if (isset($params['record_url']) && $params['record_url'] !== '') {
            $query->where('record_url', 'like', '%' . $params['record_url'] . '%');
        }

        // 执行呼叫的时间戳
        if (isset($params['create_time']) && $params['create_time'] !== '') {
            $query->where('create_time', '=', $params['create_time']);
        }

        // 时间戳
        if (isset($params['api_date']) && $params['api_date'] !== '') {
            $query->where('api_date', '=', $params['api_date']);
        }

        return $query;
    }
}
