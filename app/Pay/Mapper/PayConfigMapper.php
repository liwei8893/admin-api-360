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
        if (isset($params['app_id']) && $params['app_id'] !== '') {
            $query->where('app_id', 'like', '%' . $params['app_id'] . '%');
        }

        // 公众号 APPID
        if (isset($params['mp_app_id']) && $params['mp_app_id'] !== '') {
            $query->where('mp_app_id', 'like', '%' . $params['mp_app_id'] . '%');
        }

        // 小程序 APPID
        if (isset($params['mini_app_id']) && $params['mini_app_id'] !== '') {
            $query->where('mini_app_id', 'like', '%' . $params['mini_app_id'] . '%');
        }

        // 商户号
        if (isset($params['mch_id']) && $params['mch_id'] !== '') {
            $query->where('mch_id', 'like', '%' . $params['mch_id'] . '%');
        }

        // 商户秘钥 API v3 密钥
        if (isset($params['mch_secret_key']) && $params['mch_secret_key'] !== '') {
            $query->where('mch_secret_key', 'like', '%' . $params['mch_secret_key'] . '%');
        }

        // 商户私钥API证书 PRIVATE KEY
        if (isset($params['mch_secret_cert']) && $params['mch_secret_cert'] !== '') {
            $query->where('mch_secret_cert', 'like', '%' . $params['mch_secret_cert'] . '%');
        }

        // 商户公钥证书路径API证书 CERTIFICATE
        if (isset($params['mch_public_cert_path']) && $params['mch_public_cert_path'] !== '') {
            $query->where('mch_public_cert_path', 'like', '%' . $params['mch_public_cert_path'] . '%');
        }

        // v2商户秘钥
        if (isset($params['key']) && $params['key'] !== '') {
            $query->where('key', 'like', '%' . $params['key'] . '%');
        }

        return $query;
    }
}
