<?php

declare(strict_types=1);

namespace App\Sta\Crontab;

use App\Sta\Service\DailyService;
use App\Sta\Service\StaAccessLogService;
use App\Sta\Service\StaService;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;

class DailyStatistics
{
    #[Inject]
    protected StaService $staService;

    #[Inject]
    protected StaAccessLogService $accessLogService;

    #[Inject]
    protected DailyService $service;

    /**
     * 每十分钟查询一次会员新增,会员总数,点击量,存入daily_statistics表,空间换时间.
     */
    public function execute(): bool
    {
        // 当天开始时间,结束时间
        $params['start_time'] = Carbon::now()->startOfDay()->timestamp;
        $params['end_time'] = Carbon::now()->endOfDay()->timestamp;
        // 当日新增用户数
        $addUser = $this->staService->getUsersTotal($params)['count'];
        // 总用户数
        $totalUser = $this->staService->getUsersTotal([])['count'];
        // 点击量
        $hits = $this->accessLogService->getAccessLogTotal($params)['count'];

        return $this->service->updateOrInsertDailySta([
            'hits' => $hits,
            'add_user' => $addUser,
            'total_user' => $totalUser,
        ]);
    }
}
