<?php

declare(strict_types=1);

namespace App\Operation\Crontab;

use App\Operation\Service\WxMsgService;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SendWxMsgCrontab
{
    /**
     * @throws ContainerExceptionInterface
     * @throws JsonException|NotFoundExceptionInterface
     */
    public function execute(): void
    {
        $wxMsgService = container()->get(WxMsgService::class);
        $wxMsgService->addQueueWxMsg();
    }
}
