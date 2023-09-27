<?php

declare(strict_types=1);

namespace App\Sta\Service;

use App\Sta\Mapper\DailyMapper;
use Hyperf\Database\Model\Collection;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

class DailyService extends AbstractService
{
    /**
     * @var DailyMapper
     */
    #[Inject]
    public $mapper;

    public function setDailyHits(array $params): bool
    {
        return $this->mapper->setDailyHits($params);
    }

    public function getDailyHits(): Collection|array|\Hyperf\Collection\Collection
    {
        return $this->mapper->getDailyHits();
    }
}
