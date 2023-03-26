<?php

declare(strict_types=1);

namespace App\System\Crontab;

use App\System\Service\AreaService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class SetAllAreaNullUser
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function execute(): void
    {
        $areaService = container()->get(AreaService::class);
        $areaService->setAllAreaNullUser();
    }
}
