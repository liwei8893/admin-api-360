<?php

declare(strict_types=1);

use App\System\Service\SystemQueueLogService;
use App\System\Vo\QueueMessageVo;
use Hyperf\Context\ApplicationContext;
use Hyperf\Context\Context;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\LoggerFactory;
use Mine\Helper\AppVerify;
use Mine\Helper\Id;
use Mine\Helper\LoginUser;
use Mine\MineCollection;
use Mine\MineRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use function Hyperf\Translation\__;

if (!function_exists('container')) {
    /**
     * 获取容器实例.
     */
    function container(): Psr\Container\ContainerInterface
    {
        return ApplicationContext::getContainer();
    }
}

if (!function_exists('redis')) {
    /**
     * 获取Redis实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function redis(): Hyperf\Redis\Redis
    {
        return container()->get(\Hyperf\Redis\Redis::class);
    }
}

if (!function_exists('console')) {
    /**
     * 获取控制台输出实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function console(): StdoutLoggerInterface
    {
        return container()->get(StdoutLoggerInterface::class);
    }
}

if (!function_exists('logger')) {
    /**
     * 获取日志实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function logger(string $name = 'Log'): LoggerInterface
    {
        return container()->get(LoggerFactory::class)->get($name);
    }
}

if (!function_exists('user')) {
    /**
     * 获取当前登录用户实例.
     */
    function user(string $scene = 'default'): LoginUser
    {
        return new LoginUser($scene);
    }
}

if (!function_exists('format_size')) {
    /**
     * 格式化大小.
     */
    function format_size(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $index = 0;
        for ($i = 0; $size >= 1024 && $i < 5; ++$i) {
            $size /= 1024;
            $index = $i;
        }
        return round($size, 2) . $units[$index];
    }
}

if (!function_exists('t')) {
    /**
     * 多语言函数.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function t(string $key, array $replace = []): string
    {
        $acceptLanguage = container()->get(MineRequest::class)->getHeaderLine('accept-language');
        $language = !empty($acceptLanguage) ? explode(',', $acceptLanguage)[0] : 'zh_CN';
        return __($key, $replace, $language);
    }
}

if (!function_exists('mine_collect')) {
    /**
     * 创建一个Mine的集合类.
     * @param null|mixed $value
     */
    function mine_collect($value = null): Mine\MineCollection
    {
        return new MineCollection($value);
    }
}

if (!function_exists('context_set')) {
    /**
     * 设置上下文数据.
     * @param mixed $data
     */
    function context_set(string $key, $data): bool
    {
        return (bool)Context::set($key, $data);
    }
}

if (!function_exists('context_get')) {
    /**
     * 获取上下文数据.
     * @return mixed
     */
    function context_get(string $key)
    {
        return Context::get($key);
    }
}

if (!function_exists('app_verify')) {
    /**
     * 获取APP应用请求实例.
     */
    function app_verify(string $scene = 'api'): AppVerify
    {
        return new AppVerify($scene);
    }
}

if (!function_exists('snowflake_id')) {
    /**
     * 生成雪花ID.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    function snowflake_id(?int $workerId = null): int
    {
        return container()->get(Id::class)->getId($workerId);
    }
}

if (!function_exists('event')) {
    /**
     * 事件调度快捷方法.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function event(object $dispatch): object
    {
        return container()->get(EventDispatcherInterface::class)->dispatch($dispatch);
    }
}

if (!function_exists('push_queue_message')) {
    /**
     * 推送消息到队列.
     * @throws Throwable
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function push_queue_message(QueueMessageVo $message, array $receiveUsers = []): bool
    {
        return container()
            ->get(SystemQueueLogService::class)
            ->pushMessage($message, $receiveUsers);
    }
}

if (!function_exists('add_queue')) {
    /**
     * 添加任务到队列.
     * @throws Throwable
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function add_queue(App\System\Vo\AmqpQueueVo $amqpQueueVo): bool
    {
        return container()
            ->get(SystemQueueLogService::class)
            ->addQueue($amqpQueueVo);
    }
}
