<?php

declare(strict_types=1);

namespace App\System\Queue\Consumer;

use Hyperf\AsyncQueue\Process\ConsumerProcess;
use Hyperf\Process\Annotation\Process;

/**
 * 后台内部消息队列消费处理.
 */
#[Process(name: "async-queue")]
class MessageConsumer extends ConsumerProcess
{
}
