<?php

declare(strict_types=1);

namespace App\Sta\Service;

use App\Sta\Mapper\DailyMapper;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

class DailyService extends AbstractService
{
    /**
     * @var DailyMapper
     */
    #[Inject]
    public $mapper;

    public function updateOrInsertDailySta(array $params): bool
    {
        return $this->mapper->updateOrInsertDailySta($params);
    }

    /**
     * 获取列表数据.
     */
    public function getList(?array $params = null, bool $isScope = false): array
    {
        if (! isset($params['start_date'], $params['end_date'])) {
            $params['start_date'] = Carbon::now()->subDays(6)->toDateString();
            $params['end_date'] = Carbon::now()->toDateString();
        }
        return parent::getList($params, $isScope);
    }
}
