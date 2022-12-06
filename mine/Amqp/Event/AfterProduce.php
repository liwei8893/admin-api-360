<?php

declare(strict_types=1);

namespace Mine\Amqp\Event;

use Hyperf\Amqp\Message\ProducerMessageInterface;

class AfterProduce
{
    public $producer;

    public function __construct(ProducerMessageInterface $producer)
    {
        $this->producer = $producer;
    }
}
