<?php

declare(strict_types=1);

namespace App\Users\Mapper;

use App\Users\Model\UserRemark;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 用户备注Mapper类.
 */
class UserRemarkMapper extends AbstractMapper
{
    /**
     * @var UserRemark
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = UserRemark::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        // 关联用户ID
        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }

        // 备注类型,1常规,2售后
        if (isset($params['type']) && $params['type'] !== '') {
            $query->where('type', '=', $params['type']);
        }

        // 售后类型,1承诺一对一,2直播课,3找不到课程老师,4无理由退费
        if (isset($params['after_sale_type']) && $params['after_sale_type'] !== '') {
            $query->where('after_sale_type', '=', $params['after_sale_type']);
        }

        // 创建人ID
        if (isset($params['created_by']) && $params['created_by'] !== '') {
            $query->where('created_by', '=', $params['created_by']);
        }

        if (isset($params['has_completed']) && $params['has_completed'] !== '') {
            $query->where('has_completed', '=', $params['has_completed']);
        }

        if (isset($params['created_at'][0], $params['created_at'][1])) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0] . ' 00:00:00', $params['created_at'][1] . ' 23:59:59']
            );
        }

        if (!empty($params['withUser'])) {
            $query->with('user');
        }
        if (!empty($params['withAdminUser'])) {
            $query->with('adminUser:id,username,nickname,phone');
        }
        if (isset($params['created_name'])) {
            $query->whereHas('adminUser', function (Builder $query) use ($params) {
                $query->where('nickname', 'like', "%{$params['created_name']}%");
            });
        }

        return $query;
    }
}
