<?php

declare(strict_types=1);

namespace App\System\Queue\Consumer;

use Hyperf\AsyncQueue\Process\ConsumerProcess;
use Hyperf\Process\Annotation\Process;
use function Hyperf\Support\env;

/**
 * 后台内部消息队列消费处理.
 */
#[Process(name: "async-queue")]
class MessageConsumer extends ConsumerProcess
{
    public function isEnable($server): bool
    {
        return env('AMQP_ENABLE', false);
    }
}
