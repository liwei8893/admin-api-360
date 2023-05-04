<?php

declare(strict_types=1);

namespace App\Pay\Mapper;

use App\Pay\Model\PayConfig;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 商户配置Mapper类.
 */
class PayConfigMapper extends AbstractMapper
{
    /**
     * @var PayConfig
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = PayConfig::class;
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

        // APP APPID
        if (isset($params['appid']) && $params['appid'] !== '') {
            $query->where('appid', 'like', '%' . $params['appid'] . '%');
        }

        // 公众号 APPID
        if (isset($params['app_id']) && $params['app_id'] !== '') {
            $query->where('app_id', 'like', '%' . $params['app_id'] . '%');
        }

        // 小程序 APPID
        if (isset($params['miniapp_id']) && $params['miniapp_id'] !== '') {
            $query->where('miniapp_id', 'like', '%' . $params['miniapp_id'] . '%');
        }

        // 商户号
        if (isset($params['mch_id']) && $params['mch_id'] !== '') {
            $query->where('mch_id', 'like', '%' . $params['mch_id'] . '%');
        }

        // 商户秘钥
        if (isset($params['key']) && $params['key'] !== '') {
            $query->where('key', 'like', '%' . $params['key'] . '%');
        }

        return $query;
    }
}
