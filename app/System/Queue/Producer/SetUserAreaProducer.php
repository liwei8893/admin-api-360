<?php

declare(strict_types=1);

namespace App\System\Queue\Producer;

use Hyperf\Amqp\Message\ProducerMessage;

//#[Producer(exchange: 'mineadmin', routingKey: 'area.routing')]
class SetUserAreaProducer extends ProducerMessage
{
    public function __construct($data)
    {
        $this->payload = $data;
    }
}
