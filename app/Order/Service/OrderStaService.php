<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Order\Service;

use App\Order\Mapper\OrderStaMapper;
use Hyperf\Database\Model\Collection;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

/**
 * 订单管理服务类.
 */
class OrderStaService extends AbstractService
{
    /**
     * @var OrderStaMapper
     */
    #[inject]
    public $mapper;

    /**
     * 会员新增统计
     * @param $data
     * @return array
     */
    public function getNewVipSta($data): array
    {
        $data = $this->mapper->getNewVipSta($data);
        if ($data->isEmpty()) {
            return $data->toArray();
        }
        $allPlatform = $data->pluck('platform')->unique()->values()->toArray();
        $data = $this->handleStaDate($data);
        $this->handleStaDataSum($data, $allPlatform);
        return array_values($data);
    }

    /**
     * 续费统计
     * @param $data
     * @return array
     */
    public function getRenewalSta($data): array
    {
        $data = $this->mapper->getRenewalSta($data);
        if ($data->isEmpty()) {
            return $data->toArray();
        }
        $allPlatform = $data->pluck('platform')->unique()->values()->toArray();
        $data = $this->handleStaDate($data, 'renew_day');
        $this->handleStaDataSum($data, $allPlatform);
        return array_values($data);
    }

    /**
     * 退费统计.
     * @param $data
     * @return array
     */
    public function getRefundSta($data): array
    {
        $data = $this->mapper->getRefundSta($data);
        if ($data->isEmpty()) {
            return $data->toArray();
        }
        $data->each(function ($item) {
            $item->created_at = $item->create_at;
        });
        $allPlatform = $data->pluck('platform')->unique()->values()->toArray();
        $data = $this->handleStaDate($data);
        $this->handleStaDataSum($data, $allPlatform);
        return array_values($data);
    }

    /**
     * 处理时间,created_at->Y-m-d.
     * @param $data
     * @param string $dayField
     * @return array
     */
    protected function handleStaDate($data, string $dayField = 'indate'): array
    {
        return $data->reduce(function ($carry, $item) use ($dayField) {
            $item['date'] = $item->created_at->toDateString();
            $item['tag'] = 'other';
            if ($item[$dayField] >= 100 && $item[$dayField] <= 730) {
                $item['tag'] = 'd1';
            } elseif ($item[$dayField] >= 731 && $item[$dayField] <= 1095) {
                $item['tag'] = 'd2';
            } elseif ($item[$dayField] >= 1096 && $item[$dayField] <= 1200) {
                $item['tag'] = 'd3';
            } elseif ($item[$dayField] > 1200) {
                $item['tag'] = 'd4';
            }
            if (! isset($carry[$item['date']][$item['platform']][$item['tag']])) {
                $carry[$item['date']][$item['platform']][$item['tag']] = 1;
            } else {
                ++$carry[$item['date']][$item['platform']][$item['tag']];
            }
            return $carry;
        }, []);
    }

    /**
     * 处理合计行列.
     * @param $data
     * @param $allPlatform
     */
    protected function handleStaDataSum(&$data, $allPlatform): void
    {
        // 计算合计
        $colSum = ['date' => '合计', 'sum' => 0];
        foreach ($data as $key => &$value) {
            // 平台补零
            foreach ($allPlatform as $platform) {
                if (empty($value[$platform])) {
                    $value[$platform] = ['sum' => 0];
                }
                $value[$platform]['d1'] = $value[$platform]['d1'] ?? 0;
                $value[$platform]['d2'] = $value[$platform]['d2'] ?? 0;
                $value[$platform]['d3'] = $value[$platform]['d3'] ?? 0;
                $value[$platform]['d4'] = $value[$platform]['d4'] ?? 0;
            }
            $value['sum'] = 0;
            foreach ($value as $itemKey => $item) {
                if (empty($item)) {
                    continue;
                }
                // 合计行
                $sum = ($item['d1'] ?? 0) + (! empty($item['d2']) ? ($item['d2'] * 2) : 0) + (! empty($item['d3']) ? ($item['d3'] * 3) : 0) + (! empty($item['d4']) ? ($item['d4'] * 4) : 0);
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
    }
}
