<?php

declare(strict_types=1);

namespace App\Operation\Crontab;

use App\Operation\Service\WxMsgService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SendLearningReportCrontab
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function execute(): void
    {
        $wxMsgService = container()->get(WxMsgService::class);
        $wxMsgService->sendLearningReportWxMsg();
    }
}
