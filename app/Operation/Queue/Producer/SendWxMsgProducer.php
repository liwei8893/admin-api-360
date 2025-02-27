<?php

declare(strict_types=1);

namespace App\Operation\Queue\Producer;

use Hyperf\Amqp\Message\ProducerMessage;

//#[Producer(exchange: 'mineadmin', routingKey: 'wxMsg.routing')]
class SendWxMsgProducer extends ProducerMessage
{
    public function __construct(mixed $data)
    {
        $this->payload = (is_array($data) || is_object($data)) ? json_encode($data) : $data;
    }
}
