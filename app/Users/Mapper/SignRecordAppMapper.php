<?php

declare(strict_types=1);

namespace App\Users\Mapper;

use App\Users\Model\SignRecord;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractMapper;

class SignRecordAppMapper extends AbstractMapper
{
    /**
     * @var SignRecord
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = SignRecord::class;
    }

    /**
     * 是否签到.
     */
    public function hasSignRecord($userId, $signDate): bool
    {
        return SignRecord::query()->where('user_id', $userId)->where('sign_date', $signDate)->exists();
    }

    /**
     * 最后签到日期
     * @param $userId
     * @return Model|Builder|null
     */
    public function lastSignDate($userId): Model|Builder|null
    {
        return SignRecord::query()->where('user_id', $userId)->select('sign_date')->latest('id')->first();
    }
}
