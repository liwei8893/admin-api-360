<?php

declare(strict_types=1);

namespace App\Score\Mapper;

use App\Score\Model\Avatar;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

class AvatarMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = Avatar::class;
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['id']) && ! is_array($params['id'])) {
            $query->where('id', $params['id']);
        }

        if (isset($params['id']) && is_array($params['id'])) {
            $query->whereIn('id', $params['id']);
        }
        if (! empty($params['excludeShop'])) {
            $query->has('scoreShop', '<');
        }
        return $query;
    }
}
