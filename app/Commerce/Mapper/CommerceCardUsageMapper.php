<?php

declare(strict_types=1);

namespace App\Commerce\Mapper;

use App\Commerce\Model\CommerceCardUsage;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 电商卡使用记录Mapper类.
 */
class CommerceCardUsageMapper extends AbstractMapper
{
    /**
     * @var CommerceCardUsage
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CommerceCardUsage::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 卡号
        if (isset($params['card_id']) && $params['card_id'] !== '') {
            $query->where('card_id', '=', $params['card_id']);
        }

        // 用户ID
        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }

        // 创建时间
        if (isset($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0], $params['created_at'][1]]
            );
        }

        if (! empty($params['withUser'])) {
            $query->with('user:id,user_name,mobile');
        }
        return $query;
    }
}
