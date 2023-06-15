<?php

declare(strict_types=1);

namespace App\Operation\Queue\Consumer;

use App\Operation\Service\WxMsgService;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use Hyperf\Di\Annotation\Inject;
use PhpAmqpLib\Message\AMQPMessage;

#[Consumer(exchange: 'mineadmin', routingKey: 'wxMsg.routing', queue: 'wxMsg.queue', name: 'wxMsg.queue', nums: 5)]
class SendWxMsgConsumer extends ConsumerMessage
{
    #[Inject]
    protected WxMsgService $msgService;

    public function consumeMessage($data, AMQPMessage $message): string
    {
        $result = $this->msgService->sendWxMsg($data);
        return $this->consume($result);
    }

    public function consume($data): string
    {
        return $data ? Result::ACK : Result::DROP;
    }

    /**
     * 设置是否启动amqp.
     */
    public function isEnable(): bool
    {
        return \Hyperf\Support\env('AMQP_ENABLE', false);
    }
}
