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

namespace App\Order\Service;

use App\Order\Mapper\OrderMapper;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Collection;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;

/**
 * 订单管理服务类
 */
class OrderService extends AbstractService
{
    /**
     * @var OrderMapper
     */
    #[inject]
    public $mapper;

    #[inject]
    public UsersRenewService $usersRenewService;

    /**
     * @param $params
     * author:ZQ
     * time:2022-08-18 15:35
     */
    #[Transaction]
    public function changeEndDate($params): bool
    {
        if (empty($params['day']) && in_array($params['type'], [1, 2], true)) {
            throw new NormalStatusException('参数错误:未选择有效期天数!');
        }
        if (empty($params['date']) && $params['type'] === 3) {
            throw new NormalStatusException('参数错误:未选择指定有效期!');
        }
        // 是否续费
        $query = [];
        if (!empty($params['renew']) && !empty($params['money'])) {
            $query = ['is_renew' => 1, 'renew_time' => time()];
        }
        $orderData = $this->mapper->getCollectByIds($params['ids'], ['id', 'created_at', 'indate', 'user_id', 'shop_id']);
        foreach ($orderData as $item) {
            // 增加有效期
            if ($params['type'] === 1) {
                $params['date'] = Carbon::parse($item->course_end_time)->addDays($params['day'])->toDateString();
            }
            // 减少有效期
            if ($params['type'] === 2) {
                $params['date'] = Carbon::parse($item->course_end_time)->addDays(-$params['day'])->toDateString();
            }
            // 修改到指定有效期
            $update = $this->handleEndDateToTime($item->toArray(), $params['date'], $query);
            // 插入续费记录
            $insert = $this->handleRenewData($item->toArray(), $params);
            if (!$update || !$insert) {
                throw new NormalStatusException('更新失败,请稍后重试!');
            }
        }
        return true;
    }

    /**
     * 处理指定有效期的更改
     * @param array $item
     * @param string $endDate
     * @param array $query
     * author:ZQ
     * time:2022-08-19 14:34
     * @return int
     */
    public function handleEndDateToTime(array $item, string $endDate, array $query = []): int
    {
        $oldDt = Carbon::parse(Carbon::parse($item['course_end_time'])->toDateString());
        $newDt = Carbon::parse($endDate);
        // 计算相差多少天
        $diffDay = $oldDt->diffInDays($newDt);
        // 计算出时间是加还是减,lte小于或等于
        $hasAdd = $oldDt->lte($newDt);
        if ($hasAdd) {
            return $this->mapper->incrementInDate($item['id'], $diffDay, $query);
        }
        return $this->mapper->decrementInDate($item['id'], $diffDay, $query);
    }


    /**
     * 组装数据,保存续费记录表
     * @param array $item
     * @param array $params
     * @return bool
     * author:ZQ
     * time:2022-08-19 16:06
     */
    public function handleRenewData(array $item, array $params): bool
    {
        $data = [
            'status' => $params['renew'] ? 1 : 0,
            'startDate' => $item['course_end_time'],
            'endDate' => $params['date'],
        ];
        return $this->usersRenewService->recordUserRenew(array_merge($item,$params,$data));
    }
}