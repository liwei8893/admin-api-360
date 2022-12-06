<?php

declare(strict_types=1);

namespace App\System\Mapper;

use App\System\Model\SystemApp;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractMapper;

/**
 * Class SystemAppMapper.
 */
class SystemAppMapper extends AbstractMapper
{
    /**
     * @var SystemApp
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = SystemApp::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['app_name'])) {
            $query->where('app_name', $params['app_name']);
        }

        if (isset($params['app_id'])) {
            $query->where('app_id', $params['app_id']);
        }

        if (isset($params['group_id'])) {
            $query->where('group_id', $params['group_id']);
        }

        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }
        return $query;
    }

    /**
     * 绑定接口.
     */
    public function bind(int $id, array $ids): bool
    {
        $model = $this->read($id);
        $model && $model->apis()->sync($ids);
        return true;
    }

    /**
     * 获取api列表.
     * @param int $id
     */
    public function getApiList(int $appId): array
    {
        return Db::table('system_app_api')->where('app_id', $appId)->pluck('api_id')->toArray();
    }

    /**
     * 通过app_id获取app信息和接口数据.
     */
    public function getAppAndInterfaceList(string $appId): array
    {
        return $this->model::query()->where('app_id', $appId)
            ->with(['apis' => function ($query) {
                $query->where('status', SystemApp::ENABLE);
            }])->first(['id', 'app_id', 'app_secret', 'app_name', 'updated_at', 'description'])->toArray();
    }
}
