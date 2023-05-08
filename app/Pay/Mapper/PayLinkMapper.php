<?php

declare(strict_types=1);

namespace App\Pay\Mapper;

use App\Pay\Model\PayLink;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 付款链接Mapper类.
 */
class PayLinkMapper extends AbstractMapper
{
    /**
     * @var PayLink
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = PayLink::class;
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

        // 平台编号
        if (isset($params['platform']) && $params['platform'] !== '') {
            $query->where('platform', 'like', '%' . $params['platform_code'] . '%');
        }

        // pay_config表ID
        if (isset($params['config_id']) && $params['config_id'] !== '') {
            $query->where('config_id', '=', $params['config_id']);
        }

        // pay_auth表ID
        if (isset($params['auth_id']) && $params['auth_id'] !== '') {
            $query->where('auth_id', '=', $params['auth_id']);
        }

        if (! empty($params['withCourse'])) {
            $query->with(['course:id,title']);
        }

        return $query;
    }
}
