<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\System\Service\SystemDictDataService;
use App\System\Service\SystemQueueMessageService;
use App\Users\Mapper\UserRemarkMapper;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * 用户备注服务类.
 */
class UserRemarkService extends AbstractService
{
    /**
     * @var UserRemarkMapper
     */
    public $mapper;

    #[Inject]
    protected SystemQueueMessageService $queueMessageService;

    #[Inject]
    protected SystemDictDataService $systemDictDataService;

    public function __construct(UserRemarkMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        $data['created_id'] = user()->getId();
        if (isset($data['created_at'])) {
            unset($data['created_at']);
        }
        // 发送站内消息
        if (isset($data['type']) && $data['type'] === 2) {
            try {
                $this->queueMessageService->sendMessage(['title' => '售后', 'content' => $data['remark'], 'users' => [40, 2]]);
            } catch (ContainerExceptionInterface|NotFoundExceptionInterface|Throwable $e) {
            }
        }
        return parent::save($data);
    }

    protected function handleExportData(array &$data): void
    {
        $afterSaleTypeDict = \Hyperf\Collection\collect($this->systemDictDataService->getList(['code' => 'AfterSaleType']));
        $normalType = $data['type'] === 1;
        $hasCompletedLabel = $data['has_completed'] === 0 ? '未完成' : '已完成';
        $data['type'] = $normalType ? '常规' : '售后';
        $data['after_sale_type'] = $afterSaleTypeDict->where('key', $data['after_sale_type'])->pluck('title')->first() ?? '';
        $data['has_completed'] = $normalType ? '' : $hasCompletedLabel;
        $data['created_at'] = date('Y-m-d H:i:s', (int) $data['created_at']);
        $data['updated_at'] = date('Y-m-d H:i:s', (int) $data['updated_at']);
    }
}
