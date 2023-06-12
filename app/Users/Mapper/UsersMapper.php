<?php

declare(strict_types=1);

namespace App\Users\Mapper;

use App\Users\Model\User;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractMapper;

/**
 * 用户表Mapper类.
 */
class UsersMapper extends AbstractMapper
{
    /**
     * @var User
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = User::class;
    }

    /**
     * 用手机号检测用户是否存在.
     * @param mixed $mobile
     */
    public function existsByMobile($mobile): bool
    {
        return $this->model::query()->where('mobile', $mobile)->exists();
    }

    /**
     * 用手机号查询一条数据.
     */
    public function readByMobile(string $mobile): Model|Builder|null
    {
        return $this->model::query()->where('mobile', $mobile)->first();
    }

    /**
     * 初始化用户密码,手机号后六位.
     * @param null $password
     */
    public function initUserPassword(int $id, $password = null): bool
    {
        $model = $this->model::query()->find($id);
        if ($model) {
            $model->user_pass = $password ?? $this->getInitPassword((string) $model->mobile);
            return $model->save();
        }
        return false;
    }

    /**
     * 获取初始密码,手机号后六位.
     */
    public function getInitPassword(string $mobile): string
    {
        return substr($mobile, -6);
    }

    /**
     * 获取初始用户名,手机号隐藏中间4位.
     */
    public function getInitUserName(string $mobile): string
    {
        return substr_replace($mobile, '****', 3, 4);
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['id']) && ! is_array($params['id'])) {
            $query->where('id', $params['id']);
        }
        if (isset($params['id']) && is_array($params['id'])) {
            $query->whereIn('id', $params['id']);
        }

        if (isset($params['status']) && ! is_array($params['status'])) {
            $query->where('status', $params['status']);
        }
        if (isset($params['status']) && is_array($params['status'])) {
            $query->whereIn('status', $params['status']);
        }

        if (isset($params['user_type']) && ! is_array($params['user_type'])) {
            $query->where('user_type', $params['user_type']);
        }
        if (isset($params['user_type']) && is_array($params['user_type'])) {
            $query->whereIn('user_type', $params['user_type']);
        }

        if (! empty($params['mobile'])) {
            $query->where('mobile', 'like', $params['mobile'] . '%');
        }

        if (! empty($params['old_platform'])) {
            $query->where('old_platform', 'like', $params['old_platform'] . '%');
        }

        if (! empty($params['is_teacher'])) {
            $query->where('is_teacher', $params['is_teacher']);
        }

        if (! empty($params['is_assistant'])) {
            $query->where('is_assistant', $params['is_assistant']);
        }

        if (! empty($params['user_name'])) {
            $query->where('user_name', 'like', '%' . $params['user_name'] . '%');
        }

        if (! empty($params['keywords'])) {
            $query->where(
                fn ($query) => $query->where('mobile', 'like', '%' . $params['keywords'] . '%')
                    ->orWhere('user_name', 'like', '%' . $params['keywords'] . '%')
                    ->orWhere('remark', 'like', '%' . $params['keywords'] . '%')
            );
        }

        if (isset($params['created_at'][0], $params['created_at'][1])) {
            $query->whereBetween(
                'created_at',
                [strtotime($params['created_at'][0] . ' 00:00:00'), strtotime($params['created_at'][1] . ' 23:59:59')]
            );
        }

        if (isset($params['platform']) && ! is_array($params['platform'])) {
            $query->where('platform', '=', $params['platform']);
        }

        if (isset($params['platform']) && is_array($params['platform'])) {
            $query->whereIn('platform', $params['platform']);
        }

        if (isset($params['grade_id']) && is_array($params['grade_id'])) {
            $query->whereIn('grade_id', $params['grade_id']);
        }

        if (isset($params['vipType'])) {
            if ($params['vipType'] === '0') {
                $query->whereHas(
                    'orders',
                    fn (Builder $query) => $query->normalOrder()->whereNotIn('shop_id', User::VIP_TYPE_NONE)
                );
            }
            if ($params['vipType'] === '1') {
                $query->whereHas(
                    'orders',
                    fn (Builder $query) => $query->normalOrder()->where('shop_id', User::VIP_TYPE_ENJOY)
                );
            }
            if ($params['vipType'] === '2') {
                $query->whereHas(
                    'orders',
                    fn (Builder $query) => $query->normalOrder()->where('shop_id', User::VIP_TYPE_SUPER)
                );
            }
            if ($params['vipType'] === '3') {
                $query->whereHas(
                    'orders',
                    fn (Builder $query) => $query->normalOrder()->where('shop_id', User::VIP_TYPE_SUPREME)
                );
            }
        }

        if (! empty($params['withGrades'])) {
            $query->with(['grades:label,value']);
        }

        if (! empty($params['withVipType'])) {
            $query->with(['vipType']);
        }

        if (! empty($params['withStatus'])) {
            $query->with(['status']);
        }

        if (! empty($params['withUserType'])) {
            $query->with(['userType']);
        }
        return $query;
    }
}
