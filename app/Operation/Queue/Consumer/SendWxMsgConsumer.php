<?php

declare(strict_types=1);

namespace App\Operation\Queue\Consumer;

use App\Operation\Service\WxMsgService;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use Hyperf\Di\Annotation\Inject;
use JsonException;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[Consumer(exchange: 'mineadmin', routingKey: 'wxMsg.routing', queue: 'wxMsg.queue', name: 'wxMsg.name', nums: 2)]
class SendWxMsgConsumer extends ConsumerMessage
{
    #[Inject]
    protected WxMsgService $msgService;

    public function consumeMessage($data, AMQPMessage $message): string
    {
        try {
            $messageData = json_decode($data, true);
            $result = $this->msgService->sendWxMsg($messageData);
            return $this->consume($result);
        } catch (InvalidArgumentException|JsonException|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            return $this->consume(false);
        }
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
