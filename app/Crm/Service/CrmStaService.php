<?php

namespace App\Crm\Service;

use App\Crm\Model\CrmShopOrder;
use App\Crm\Model\CrmUserCommTimeline;
use App\Users\Model\User;
use Carbon\Carbon;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;

class CrmStaService extends AbstractService
{
    // 获取简报看板
    public function getBriefingBoard(array $params): array
    {
        $startDate = $params['startDate'] ? Carbon::create($params['startDate']) : '';
        $endDate = $params['endDate'] ? Carbon::create($params['endDate']) : '';
        $result = [
            'newCustomerNum' => 0, // 新增客户数
            'totalCustomerNum' => 0, // 总客户数
            'followUpNum' => 0, // 待跟进数
            'renewalNum' => 0, // 续费数
            'orderAmount' => 0, // 订单金额
        ];
        if (empty($startDate) || empty($endDate)) {
            return $result;
        }
        // 获取新增客户数
        $result['newCustomerNum'] = User::query()->userDataScope()->platformDataScope()->whereBetween('created_at', [$startDate->timestamp, $endDate->timestamp])->count();
        // 获取总客户数
        $result['totalCustomerNum'] = User::query()->userDataScope()->platformDataScope()->count();
        // 获取待跟进数
        $result['followUpNum'] = CrmUserCommTimeline::query()->whereHas('user', function ($query) {
            $query->userDataScope()->platformDataScope();
        })->whereBetween('comm_time', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])->count();
        // 获取续费数
        // 获取订单金额
        $result['orderAmount'] = CrmShopOrder::query()->whereHas('user', function ($query) {
            $query->userDataScope()->platformDataScope();
        })->whereBetween('created_at', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->where('order_status', 7)->sum('amount');
        return $result;
    }

    public function getFollowUpNumByDate(array $params): array
    {
        $startDate = $params['startDate'] ? Carbon::create($params['startDate']) : '';
        $endDate = $params['endDate'] ? Carbon::create($params['endDate']) : '';
        if (empty($startDate) || empty($endDate)) {
            throw new NormalStatusException('请选择月份');
        }
        return CrmUserCommTimeline::query()->whereHas('user', function ($query) {
            $query->userDataScope()->platformDataScope();
        })->whereBetween('comm_time', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->selectRaw('DATE_FORMAT(comm_time, "%Y-%m-%d") as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();
    }
}
