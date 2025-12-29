<?php

declare(strict_types=1);

namespace App\System\Mapper;

use App\System\Model\SmsLog;
use App\Users\Model\User;
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
        return Smslog::query()->where('mobile', $mobile)
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
        return Smslog::query()->where('sms_ip', $ip)
            ->whereBetween('created_at', [strtotime(date('Y-m-d') . ' 00:00:00'), strtotime(date('Y-m-d') . ' 23:59:59')])
            ->get();
    }

    /**
     * 检测手机号是否已注册
     * @param string $mobile 手机号
     * @return bool true-已注册,false-未注册
     */
    public function checkMobileExists(string $mobile): bool
    {
        return User::query()->where('mobile', $mobile)->exists();
    }
}
