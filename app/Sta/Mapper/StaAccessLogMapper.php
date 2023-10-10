<?php

declare(strict_types=1);

namespace App\Sta\Mapper;

use App\Sta\Model\StaAccessLog;
use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractMapper;

class StaAccessLogMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = StaAccessLog::class;
    }

    public function setAccessLog(array $params): bool
    {
        return StaAccessLog::insert($params);
    }

    public function getAccessLogMod(array $params): Collection
    {
        $sub = StaAccessLog::query()
            ->whereBetween('time', [$params['start_time'], $params['end_time']])
            ->select(['page', 'device'])
            ->selectRaw('count(*) as count')
            ->groupBy(['page', 'device', 'client_ip']);
        return StaAccessLog::from(Db::raw("({$sub->toSql()}) as t"))
            ->mergeBindings($sub->getQuery())
            ->select(['page', 'device'])
            ->selectRaw('count(*) as count')
            ->groupBy(['page', 'device'])
            ->orderBy('device')
            ->orderBy('page')->get();
    }

    public function getAccessLogTotal(array $params): int
    {
        $sub = StaAccessLog::query()
            ->whereBetween('time', [$params['start_time'], $params['end_time']])
            ->selectRaw('count(*) as count')
            ->groupBy(['client_ip']);
        return StaAccessLog::from(Db::raw("({$sub->toSql()}) as t"))
            ->mergeBindings($sub->getQuery())->count();
    }
}
