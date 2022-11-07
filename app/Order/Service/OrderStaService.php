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

use App\Order\Mapper\OrderStaMapper;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

/**
 * 订单管理服务类
 */
class OrderStaService extends AbstractService
{
    /**
     * @var OrderStaMapper
     */
    #[inject]
    public $mapper;

    public function getStaByPlatform($data): array
    {
        // 没传月份默认当月
        if (!isset($data['dateMonth'])) {
            $data['dateMonth'] = date('Y-m');
        }
        $data = $this->mapper->getStaByPlatform($data);
        if ($data->isEmpty()) {
            return $data->toArray();
        }
        $allPlatform = $data->pluck('platform')->unique()->values()->toArray();
        // 处理时间,created_at->Y-m-d
        $data = $data->reduce(function ($carry, $item) {
            $item['date'] = $item->created_at->toDateString();
            $item['tag'] = 'other';
            if ($item['indate'] >= 100 && $item['indate'] <= 730) {
                $item['tag'] = 'd1';
            } elseif ($item['indate'] >= 731 && $item['indate'] <= 1095) {
                $item['tag'] = 'd2';
            } elseif ($item['indate'] >= 1096 && $item['indate'] <= 1200) {
                $item['tag'] = 'd3';
            } elseif ($item['indate'] > 1200) {
                $item['tag'] = 'd4';
            }
            if (!isset($carry[$item['date']][$item['platform']][$item['tag']])) {
                $carry[$item['date']][$item['platform']][$item['tag']] = 1;
            } else {
                $carry[$item['date']][$item['platform']][$item['tag']]++;
            }
            return $carry;
        }, []);
        // 计算合计
        $colSum = ['date' => '合计', 'sum' => 0];
        foreach ($data as $key => &$value) {
            // 平台补零
            foreach ($allPlatform as $platform) {
                if (empty($value[$platform])) {
                    $value[$platform] = ['sum' => 0];
                }
            }
            $value['sum'] = 0;
            foreach ($value as $itemKey => $item) {
                if (empty($item)) {
                    continue;
                }
                // 合计行
                $sum = ($item['d1'] ?? 0) + (!empty($item['d2']) ? ($item['d2'] * 2) : 0) + (!empty($item['d3']) ? ($item['d3'] * 3) : 0) + (!empty($item['d4']) ? ($item['d4'] * 4) : 0);
                $value[$itemKey]['sum'] = $sum;
                $value['sum'] += $sum;
                $colSum['sum'] += $sum;
                // 合计列
                foreach ($item as $dKey => $d) {
                    if (empty($colSum[$itemKey][$dKey])) {
                        $colSum[$itemKey][$dKey] = $d;
                    } else {
                        $colSum[$itemKey][$dKey] += $d;
                    }
                }
            }
            $value['date'] = $key;
        }
        unset($value);
        $data[] = $colSum;
        return array_values($data);
    }
}