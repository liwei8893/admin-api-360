<?php

namespace App\Order\Mapper;

use App\Order\Model\UsersRenew;
use Mine\Abstracts\AbstractMapper;

class UsersRenewMapper extends AbstractMapper
{

    public function assignModel():void
    {
       $this->model=UsersRenew::class;
    }

    /**
     * 续费表插入数据
     * @param array $value
     * @return bool
     * author:ZQ
     * time:2022-08-19 14:44
     */
    public function insert(array $value): bool
    {
       return UsersRenew::query()->insert($value);
    }
}