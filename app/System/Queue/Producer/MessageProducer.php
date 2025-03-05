<?php

declare(strict_types=1);

namespace App\System\Queue\Producer;

use App\System\Mapper\SystemQueueMessageMapper;
use App\System\Model\SystemQueueLog;
use Hyperf\AsyncQueue\Job;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 后台内部消息队列生产处理.
 */
class MessageProducer extends Job
{

    public mixed $payload;

    /**
     * @param mixed $data
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct($data)
    {
        console()->info(
            sprintf(
                'MineAdmin created queue message time at: %s, data is: %s',
                date('Y-m-d H:i:s'),
                (is_array($data) || is_object($data)) ? json_encode($data) : $data
            )
        );

        $this->payload = $data;
    }

    public function handle(): void
    {
        $logMod = SystemQueueLog::query()->create([
            'exchange_name' => 'redis',
            'routing_key_name' => 'message',
            'queue_name' => 'message',
            'queue_content' => json_encode($this->payload, JSON_THROW_ON_ERROR),
            'delay_time' => 0,
            'produce_status' => SystemQueueLog::PRODUCE_STATUS_SUCCESS,
            'consume_status' => SystemQueueLog::CONSUME_STATUS_DOING,
        ]);
        $payload = $this->payload;

        if (!isset($this->payload['queue_id'])) {
            $this->payload = ['queue_id' => $logMod->id, 'data' => $payload];
            $logMod->queue_content = json_encode($this->payload, JSON_THROW_ON_ERROR);
            $logMod->save();
        }
        // 根据参数处理具体逻辑
        // 通过具体参数获取模型等
        // 这里的逻辑会在 ConsumerProcess 进程中执行
        $data = $this->payload['data'];
        $data['content_id'] = $logMod->id;
        (new SystemQueueMessageMapper())->save($data);
        $logMod->consume_status = SystemQueueLog::CONSUME_STATUS_SUCCESS;
        $logMod->save();
    }
}
