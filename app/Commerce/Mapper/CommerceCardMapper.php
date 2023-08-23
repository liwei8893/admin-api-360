<?php

declare(strict_types=1);

namespace App\Commerce\Mapper;

use App\Commerce\Model\CommerceCard;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractMapper;

/**
 * 电商管理Mapper类.
 */
class CommerceCardMapper extends AbstractMapper
{
    /**
     * @var CommerceCard
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CommerceCard::class;
    }

    public function findCardByCardId(int $cardId): Model|CommerceCard|Builder|null
    {
        return CommerceCard::query()->where('card_id', $cardId)->first();
    }

    public function batchInsertData($data): bool
    {
        return CommerceCard::query()->insert($data);
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

        // 课程ID
        if (isset($params['course_id']) && $params['course_id'] !== '') {
            $query->where('course_id', '=', $params['course_id']);
        }

        // 是否使用
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', '=', $params['status']);
        }

        // 创建时间
        if (isset($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0], $params['created_at'][1]]
            );
        }

        if (! empty($params['withCourse'])) {
            $query->with('course:id,title');
        }
        return $query;
    }
}
