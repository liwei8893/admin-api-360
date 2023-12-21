<?php

declare(strict_types=1);

namespace App\System\Queue\Consumer;

use App\System\Service\AreaService;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use Hyperf\Di\Annotation\Inject;
use PhpAmqpLib\Message\AMQPMessage;

#[Consumer(exchange: 'mineadmin', routingKey: 'area.routing', queue: 'area.queue', name: 'area.queue', nums: 1)]
class SetUserAreaConsumer extends ConsumerMessage
{
    #[Inject]
    protected AreaService $areaService;

    public function consumeMessage($data, AMQPMessage $message): Result
    {
        $result = $this->areaService->setUserAreaByMobile($data);
        return $this->consume($result);
    }

    public function consume($data): Result
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
