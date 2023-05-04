<?php

declare(strict_types=1);

namespace App\Pay\Mapper;

use App\Pay\Model\PayAuth;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 公众号配置Mapper类.
 */
class PayAuthMapper extends AbstractMapper
{
    /**
     * @var PayAuth
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = PayAuth::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        // 备注
        if (isset($params['remark']) && $params['remark'] !== '') {
            $query->where('remark', 'like', '%' . $params['remark'] . '%');
        }

        // 公众号appid
        if (isset($params['appid']) && $params['appid'] !== '') {
            $query->where('appid', 'like', '%' . $params['appid'] . '%');
        }

        return $query;
    }
}
