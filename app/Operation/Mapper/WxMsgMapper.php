<?php

declare(strict_types=1);

namespace App\Operation\Mapper;

use App\Operation\Model\WxMsg;
use App\Users\Model\User;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractMapper;

/**
 * 微信消息Mapper类.
 */
class WxMsgMapper extends AbstractMapper
{
    /**
     * @var WxMsg
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = WxMsg::class;
    }

    public function getFirstUnsentMsg(): WxMsg|Model|Builder|null
    {
        return $this->model::query()
            ->where('status', WxMsg::UNSENT)
            ->where('send_time', '<=', time())
            ->first();
    }

    public function getSendUsers(): Collection|array
    {
        return User::query()
            ->select(['id', 'user_name', 'mobile', 'wxgzh_openid'])
            ->where('wxgzh_openid', '!=', '')
            // 测试，查内部人
            ->where('user_type', 0)
            ->where('id', 83775)
            ->whereHas('orders', function (Builder $query) {
                $query->where('shop_id', User::VIP_TYPE_SUPER)
                    ->NormalOrder()->IsNotExpire();
            })
            ->get();
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        // 课程标题
        if (isset($params['title']) && $params['title'] !== '') {
            $query->where('title', 'like', '%' . $params['title'] . '%');
        }

        // 0未发送 1已发送
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', '=', $params['status']);
        }

        // 发送时间
        if (isset($params['send_time']) && $params['send_time'] !== '') {
            $query->where('send_time', '=', $params['send_time']);
        }

        return $query;
    }
}
