<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\Users\Mapper;

use App\Users\Model\Users;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 用户表Mapper类
 */
class UsersMapper extends AbstractMapper
{
    /**
     * @var Users
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Users::class;
    }

    /**
     * 用手机号检测用户是否存在
     * @param $mobile
     * @return bool
     * author:ZQ
     * time:2022-05-31 14:05
     */
    public function existsByMobile($mobile): bool
    {
        return $this->model::where('mobile', $mobile)->exists();
    }

    /**
     * 初始化用户密码,手机号后六位
     * @param int $id
     * @param $password
     * @return bool
     */
    public function initUserPassword(int $id, $password = null): bool
    {
        $model = $this->model::find($id);
        if ($model) {
            $model->user_pass = $password ?? $this->getInitPassword($model->mobile);
            return $model->save();
        }
        return false;
    }

    /**
     * 获取初始密码,手机号后六位
     * @param $mobile
     * @return string
     * author:ZQ
     * time:2022-06-01 15:37
     */
    public function getInitPassword($mobile): string
    {
        return substr($mobile, -6);
    }

    /**
     * 获取初始用户名,手机号隐藏中间4位
     * @param $mobile
     * @return string
     * author:ZQ
     * time:2022-08-16 14:26
     */
    public function getInitUserName($mobile): string
    {
        return substr_replace($mobile, '****', 3, 4);
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['status']) && !is_array($params['status'])) {
            $query->where('status', $params['status']);
        }
        if (isset($params['status']) && is_array($params['status'])) {
            $query->whereIn('status', $params['status']);
        }

        if (isset($params['user_type']) && !is_array($params['user_type'])) {
            $query->where('user_type', $params['user_type']);
        }
        if (isset($params['user_type']) && is_array($params['user_type'])) {
            $query->whereIn('user_type', $params['user_type']);
        }

        if (isset($params['mobile'])) {
            $query->where('mobile', 'like', $params['mobile'] . '%');
        }

        if (isset($params['user_name'])) {
            $query->where('user_name', 'like', '%' . $params['user_name'] . '%');
        }

        if (isset($params['keywords'])) {
            $query->where(fn($query) => $query->where('mobile', 'like', '%' . $params['keywords'] . '%')
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

        if (isset($params['platform']) && !is_array($params['platform'])) {
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
                $query->whereHas('orders',
                    fn(Builder $query) => $query->normalOrder()->whereNotIn('shop_id', $this->model::VIP_TYPE_NONE)
                );
            }
            if ($params['vipType'] === '1') {
                $query->whereHas('orders',
                    fn(Builder $query) => $query->normalOrder()->where('shop_id', $this->model::VIP_TYPE_ENJOY)
                );
            }
            if ($params['vipType'] === '2') {
                $query->whereHas('orders',
                    fn(Builder $query) => $query->normalOrder()->where('shop_id', $this->model::VIP_TYPE_SUPER)
                );
            }
            if ($params['vipType'] === '3') {
                $query->whereHas('orders',
                    fn(Builder $query) => $query->normalOrder()->where('shop_id', $this->model::VIP_TYPE_SUPREME)
                );
            }
        }

        if (!empty($params['withGrades'])) {
            $query->with(['grades:value,label']);
        }

        if (!empty($params['withVipType'])) {
            $query->with(['vipType']);
        }
        return $query;
    }


}