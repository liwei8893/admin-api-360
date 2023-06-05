<?php

declare(strict_types=1);

namespace App\Operation\Queue\Producer;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;
use JsonException;

#[Producer(exchange: 'mineadmin', routingKey: 'wxMsg.routing')]
class SendWxMsgProducer extends ProducerMessage
{
    /**
     * @throws JsonException
     */
    public function __construct(mixed $data)
    {
        $this->payload = (is_array($data) || is_object($data)) ? json_encode($data, JSON_THROW_ON_ERROR) : $data;
    }
}
