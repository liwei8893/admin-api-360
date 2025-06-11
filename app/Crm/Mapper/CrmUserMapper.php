<?php

namespace App\Crm\Mapper;

use App\System\Model\SystemUser;
use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

class CrmUserMapper extends AbstractMapper
{

    public function assignModel(): void
    {
        $this->model = User::class;
    }

    public function getPageList(?array $params, bool $isScope = true, string $pageName = 'page'): array
    {
        $page = $params[$pageName] ?? 1;
        $pageSize = $params['pageSize'] ?? $this->model::PAGE_SIZE;
        $paginate = $this->listQuerySetting($params, $isScope)
            ->userDataScope()
            ->paginate(
                (int)$pageSize,
                ['*'],
                $pageName,
                (int)$page
            );
        return $this->setPaginate($paginate);
    }

    public function systemUserIndex(array $params): array
    {
        $query = SystemUser::query()
            ->join('system_user_dept', 'system_user_dept.user_id', '=', 'system_user.id')
            ->leftJoin('system_dept', 'system_dept.id', '=', 'system_user_dept.dept_id')
            ->select(['system_user.*'])
            ->platformDataScope();
        if (isset($params['dept_id'])) {
            $query->where('system_user_dept.dept_id', '=', $params['dept_id']);
        }
        // username
        if (isset($params['username']) && $params['username'] !== '') {
            $query->where('system_user.username', 'like', '%' . $params['username'] . '%');
        }
        // phone
        if (isset($params['phone']) && $params['phone'] !== '') {
            $query->where('system_user.phone', 'like', '%' . $params['phone'] . '%');
        }
        // status
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('system_user.status', '=', $params['status']);
        }
        $perPage = $params['pageSize'] ?? $this->model::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $query = $query->paginate(
            (int)$perPage,
            ['*'],
            'page',
            (int)$page
        );
        return $this->setPaginate($query);
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        // 今日新增数据
        if (isset($params['search_type']) && $params['search_type'] === '1') {
            $startDate = Carbon::now()->startOfDay()->timestamp;
            $endDate = Carbon::now()->endOfDay()->timestamp;
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        // 本周新数据
        if (isset($params['search_type']) && $params['search_type'] === '2') {
            $startDate = Carbon::now()->startOfWeek()->startOfDay()->timestamp;
            $endDate = Carbon::now()->endOfWeek()->endOfDay()->timestamp;
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        // 今日跟进
        if (isset($params['search_type']) && $params['search_type'] === '3') {
            $query->whereHas('userCommTimeline', function ($query) {
                $startDate = Carbon::now()->startOfDay()->toDateTimeString();
                $endDate = Carbon::now()->endOfDay()->toDateTimeString();
                $query->whereBetween('comm_time', [$startDate, $endDate]);
            });
        }
        // 本周跟进
        if (isset($params['search_type']) && $params['search_type'] === '4') {
            $query->whereHas('userCommTimeline', function ($query) {
                $startDate = Carbon::now()->startOfWeek()->startOfDay()->toDateTimeString();
                $endDate = Carbon::now()->endOfWeek()->endOfDay()->toDateTimeString();
                $query->whereBetween('comm_time', [$startDate, $endDate]);
            });
        }

        // 查询大单客服
        if (isset($params['created_by'])) {
            $query->where('created_by', $params['created_by']);
        }

        if (isset($params['has_created_by']) && $params['has_created_by'] === '1') {
            $query->where('created_by', '=', 0);
        }
        if (isset($params['has_created_by']) && $params['has_created_by'] === '2') {
            $query->where('created_by', '!=', 0);
        }

        if (isset($params['app']) && is_array($params['app'])) {
            $strApp = implode("|", $params['app']);
            $query->whereRaw("CONCAT(',',app,',') REGEXP ',({$strApp}),'");
        }
        if (isset($params['id']) && !is_array($params['id'])) {
            $query->where('id', $params['id']);
        }
        if (isset($params['id']) && is_array($params['id'])) {
            $query->whereIn('id', $params['id']);
        }

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

        if (!empty($params['mobileEq'])) {
            $query->where('mobile', '=', $params['mobileEq']);
        }
        if (!empty($params['oldPlatformEq'])) {
            $query->where('old_platform', '=', $params['oldPlatformEq']);
        }
        if (!empty($params['userNameEq'])) {
            $query->where('user_name', '=', $params['userNameEq']);
        }

        if (!empty($params['mobile'])) {
            $query->where('mobile', 'like', $params['mobile'] . '%');
        }

        if (!empty($params['old_platform'])) {
            $query->where('old_platform', 'like', $params['old_platform'] . '%');
        }

        if (!empty($params['is_teacher'])) {
            $query->where('is_teacher', $params['is_teacher']);
        }

        if (!empty($params['is_assistant'])) {
            $query->where('is_assistant', $params['is_assistant']);
        }

        if (!empty($params['user_name'])) {
            $query->where('user_name', 'like', '%' . $params['user_name'] . '%');
        }

        if (!empty($params['keywords'])) {
            $query->where(
                fn($query) => $query->where('mobile', 'like', '%' . $params['keywords'] . '%')
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

        if (isset($params['grade_id']) && !is_array($params['grade_id'])) {
            $query->where('grade_id', $params['grade_id']);
        }
        if (isset($params['grade_id']) && is_array($params['grade_id'])) {
            $query->whereIn('grade_id', $params['grade_id']);
        }

        if (isset($params['vipType'])) {
            if ($params['vipType'] === '0') {
                $query->whereHas(
                    'orders',
                    fn(Builder $query) => $query->normalOrder()->notVipOrder()
                );
            }
            if ($params['vipType'] === '1') {
                $query->whereHas(
                    'orders',
                    fn(Builder $query) => $query->normalOrder()->where('shop_id', User::VIP_TYPE_ENJOY)
                );
            }
            if ($params['vipType'] === '2') {
                $query->whereHas(
                    'orders',
                    fn(Builder $query) => $query->normalOrder()->vipOrder()
                );
            }
            if ($params['vipType'] === '3') {
                $query->whereHas(
                    'orders',
                    fn(Builder $query) => $query->normalOrder()->where('shop_id', User::VIP_TYPE_SUPREME)
                );
            }
        }

        if (!empty($params['withGrades'])) {
            $query->with(['grades:label,value']);
        }

        if (!empty($params['withVipType'])) {
            $query->with(['vipType']);
        }

        if (!empty($params['withStatus'])) {
            $query->with(['status']);
        }

        if (!empty($params['withUserType'])) {
            $query->with(['userType']);
        }
        return $query;
    }
}
