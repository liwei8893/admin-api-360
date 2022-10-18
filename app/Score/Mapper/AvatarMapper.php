<?php

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
        if (!empty($params['excludeShop'])) {
            $query->has('scoreShop', '<');
        }
        return $query;
    }

}