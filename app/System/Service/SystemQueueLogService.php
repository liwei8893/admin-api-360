<?php

declare(strict_types=1);

namespace App\System\Service;

use App\System\Mapper\SystemQueueLogMapper;
use App\System\Model\SystemUser;
use App\System\Queue\Producer\MessageProducer;
use App\System\Vo\AmqpQueueVo;
use App\System\Vo\QueueMessageVo;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Amqp\DelayProducer;
use Mine\Exception\NormalStatusException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * 队列管理服务类.
 */
class SystemQueueLogService extends AbstractService
{
    /**
     * @var SystemQueueLogMapper
     */
    public $mapper;

    #[Inject]
    protected SystemUserService $userService;

    #[Inject]
    protected DelayProducer $producer;

    protected DriverInterface $driver;

    /**
     * SystemQueueLogService constructor.
     */
    public function __construct(SystemQueueLogMapper $mapper, DriverFactory $driverFactory)
    {
        $this->mapper = $mapper;
        $this->driver = $driverFactory->get('default');
    }

    /**
     * 修改队列日志的生产状态
     */
    public function updateProduceStatus(string $ids): bool
    {
        // TODO...
        return true;
    }

    /**
     * 添加任务到队列.
     * @throws Throwable
     */
    public function addQueue(AmqpQueueVo $amqpQueueVo): bool
    {
        $class = $amqpQueueVo->getProducer();

        return $this->driver->push(new $class($amqpQueueVo->getData()));
    }

    /**
     * 推送消息到队列.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function pushMessage(QueueMessageVo $message, array $receiveUsers = []): bool
    {

        if (empty($message->getTitle())) {
            throw new NormalStatusException(t('system.queue_missing_message_title'), 500);
        }

        if (empty($message->getContent())) {
            throw new NormalStatusException(t('system.queue_missing_message_content_type'), 500);
        }

        if (empty($message->getContentType())) {
            throw new NormalStatusException(t('system.queue_missing_content'), 500);
        }

        if (empty($receiveUsers)) {
            $receiveUsers = $this->userService->pluck(['status' => SystemUser::USER_NORMAL], 'id');
        }

        $data = [
            'title' => $message->getTitle(),
            'content' => $message->getContent(),
            'content_type' => $message->getContentType(),
            'send_by' => $message->getSendBy() ?: user()->getId(),
            'receive_users' => $receiveUsers,
        ];
        return $this->driver->push(new MessageProducer($data));
    }
}
