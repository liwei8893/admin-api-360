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

namespace App\Crm\Service;

use App\Crm\Mapper\CrmUserTimelineMapper;
use App\Crm\Model\CrmUserTimeline;
use Mine\Abstracts\AbstractService;

/**
 * 用户时间线记录表服务类
 */
class CrmUserTimelineService extends AbstractService
{
    /**
     * @var CrmUserTimelineMapper
     */
    public $mapper;

    public function __construct(CrmUserTimelineMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 保存用户创建事件
     * @param int $userId
     * @param int $createdBy
     * @param string|null $detail
     * @return bool
     */
    public function saveCreatedUserEvent(int $userId, int $createdBy, string $detail = null): bool
    {
        $data = [
            'user_id' => $userId,
            'created_by' => $createdBy,
            'event' => '注册账号',
            'event_detail' => $detail,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return CrmUserTimeline::query()->insert($data);
    }

    /**
     * 保存用户分配事件
     * @param int $userId
     * @param int $createdBy
     * @param string $detail
     * @return bool
     */
    public function saveDistroUserEvent(int $userId, int $createdBy, string $detail): bool
    {
        $data = [
            'user_id' => $userId,
            'created_by' => $createdBy,
            'event' => '分配用户',
            'event_detail' => $detail,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return CrmUserTimeline::query()->insert($data);
    }
}
