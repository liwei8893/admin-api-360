<?php

declare(strict_types=1);

namespace App\Sta\Service;

use App\Sta\Mapper\StaAccessLogMapper;
use Hyperf\Collection\Collection;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Helper\Str;
use Mine\Helper\Tool;
use Mine\MineRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class StaAccessLogService extends AbstractService
{
    /**
     * @var StaAccessLogMapper
     */
    #[Inject]
    public $mapper;

    /**
     * 添加C端访问日志.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setAccessLog(array $params): bool
    {
        $request = container()->get(MineRequest::class);
        $agent = $request->getHeader('user-agent')[0];
        $ip = $request->ip();
        if ($ip === '127.0.0.1') {
            return true;
        }
        // 时间
        $params['time'] = time();
        // 客户端IP
        $params['client_ip'] = $ip;
        // 地区
        $params['region'] = Str::ipToRegion($ip);
        // 浏览器
        $params['browser'] = Tool::browser($agent);
        // 入库
        return $this->mapper->setAccessLog($params);
    }

    public function getAccessLogMod(array $params): Collection
    {
        return $this->mapper->getAccessLogMod($params);
    }

    public function getAccessLogTotal(array $params): array
    {
        $count = $this->mapper->getAccessLogTotal($params);
        return ['count' => $count];
    }
}
