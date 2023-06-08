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

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        $data['create_time'] = time();
        return parent::save($data);
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
//            ->where('user_type', 0)
//            ->whereIn('id', [83775, 133690])
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
        if (isset($params['send_time'][0], $params['send_time'][1])) {
            $query->whereBetween(
                'send_time',
                [strtotime($params['send_time'][0]), strtotime($params['send_time'][1])]
            );
        }
        return $query;
    }
}
