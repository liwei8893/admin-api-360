<?php


declare(strict_types=1);

namespace Mine\Crontab;

use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;

class MineCrontabStrategy
{
    /**
     * MineCrontabManage.
     */
    #[Inject]
    protected MineCrontabManage $mineCrontabManage;

    /**
     * MineExecutor.
     */
    #[Inject]
    protected MineExecutor $executor;

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function dispatch(MineCrontab $crontab)
    {
        \Hyperf\Coroutine\co(function () use ($crontab) {
            if ($crontab->getExecuteTime() instanceof Carbon) {
                $wait = $crontab->getExecuteTime()->getTimestamp() - time();
                $wait > 0 && \Swoole\Coroutine::sleep($wait);
                $this->executor->execute($crontab);
            }
        });
    }

    /**
     * 执行一次
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function executeOnce(MineCrontab $crontab)
    {
        \Hyperf\Coroutine\co(function () use ($crontab) {
            $this->executor->execute($crontab);
        });
    }
}
