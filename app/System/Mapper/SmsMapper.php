<?php

declare(strict_types=1);

namespace App\System\Mapper;

use App\System\Model\SmsLog;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
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

    /**
     * 发送短信之后设置日志.
     * @param mixed $params
     */
    public function setSmsLog(array $params): Model|Builder
    {
        return SmsLog::query()->create($params);
    }

    /**
     * 获取手机号当天发送的短信
     * @param $mobile
     * @return Collection|array
     */
    public function getTheDayByMobile($mobile): Collection|array
    {
        return Smslog::where('mobile', $mobile)
            ->whereBetween('created_at', [strtotime(date('Y-m-d') . ' 00:00:00'), strtotime(date('Y-m-d') . ' 23:59:59')])
            ->get();
    }

    /**
     * 获取ip当天发送的短信
     * @param $ip
     * @return Collection|array
     */
    public function getTheDayByIp($ip): Collection|array
    {
        return Smslog::where('sms_ip', $ip)
            ->whereBetween('created_at', [strtotime(date('Y-m-d') . ' 00:00:00'), strtotime(date('Y-m-d') . ' 23:59:59')])
            ->get();
    }
}
