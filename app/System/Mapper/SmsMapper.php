<?php

declare(strict_types=1);

namespace App\System\Mapper;

use App\System\Model\SmsLog;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractMapper;

class SmsMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = SmsLog::class;
    }

    public function checkSmsCaptcha($mobile): Model|Builder|null
    {
        return SmsLog::query()->where('mobile', $mobile)
            ->orderBy('id', 'desc')
            ->first(['sms_code', 'created_at']);
    }
}
