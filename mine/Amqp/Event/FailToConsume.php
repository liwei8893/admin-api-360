<?php

declare(strict_types=1);

namespace Mine\Amqp\Event;

use Hyperf\Amqp\Message\ConsumerMessageInterface;
use Throwable;

class FailToConsume
{
    /**
     * @var Throwable
     */
    public $throwable;

    /**
     * @var ConsumerMessageInterface
     */
    public $message;

    public $data;

    public function __construct($message, $data, Throwable $throwable)
    {
        $this->throwable = $throwable;
        $this->message = $message;
        $this->data = $data;
    }

    public function getThrowable(): Throwable
    {
        return $this->throwable;
    }
}
