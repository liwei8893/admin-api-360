<?php

namespace App\Crm\Service;

use App\Crm\Mapper\CrmStaMapper;
use App\Crm\Model\CrmShopOrder;
use App\Crm\Model\CrmUserCommTimeline;
use App\System\Model\SystemUser;
use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Mine\MineModel;

class CrmStaService extends AbstractService
{
    /**
     * @var CrmStaMapper
     */
    public $mapper;

    public function __construct(CrmStaMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    // 获取简报看板
    public function getBriefingBoard(array $params): array
    {
        $startDate = $params['startDate'] ? Carbon::create($params['startDate']) : '';
        $endDate = $params['endDate'] ? Carbon::create($params['endDate']) : '';
        $result = [
            'newCustomerNum' => 0, // 新增客户数
            'totalCustomerNum' => 0, // 总客户数
            'followUpNum' => 0, // 待跟进数
            'renewalNum' => 0, // 续费数
            'orderAmount' => 0, // 订单金额
        ];
        if (empty($startDate) || empty($endDate)) {
            return $result;
        }
        // 获取新增客户数
        $result['newCustomerNum'] = User::query()->userDataScope()->platformDataScope()->whereBetween('created_at', [$startDate->timestamp, $endDate->timestamp])->count();
        // 获取总客户数
        $result['totalCustomerNum'] = User::query()->userDataScope()->platformDataScope()->count();
        // 获取待跟进数
        $result['followUpNum'] = CrmUserCommTimeline::query()->whereHas('user', function ($query) {
            $query->userDataScope()->platformDataScope();
        })->whereBetween('comm_time', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])->count();
        // 获取续费数
        // 获取订单金额
        $result['orderAmount'] = CrmShopOrder::query()->whereHas('user', function ($query) {
            $query->userDataScope()->platformDataScope();
        })->whereBetween('created_at', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->where('order_status', 7)->sum('amount');
        return $result;
    }

    public function getFollowUpNumByDate(array $params): array
    {
        $startDate = $params['startDate'] ? Carbon::create($params['startDate']) : '';
        $endDate = $params['endDate'] ? Carbon::create($params['endDate']) : '';
        if (empty($startDate) || empty($endDate)) {
            throw new NormalStatusException('请选择月份');
        }
        return CrmUserCommTimeline::query()->whereHas('user', function ($query) {
            $query->userDataScope()->platformDataScope();
        })->whereBetween('comm_time', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->selectRaw('DATE_FORMAT(comm_time, "%Y-%m-%d") as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();
    }

    public function getConversionStaByPersonal(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;

        $posSubQuery = SystemUser::query()
            ->leftJoin('users', 'users.created_by', '=', 'system_user.id')
            ->leftJoin('crm_shop_order as cso_sub', 'cso_sub.user_id', '=', 'users.id')
            ->select([
                'system_user.id as system_user_id',
                'cso_sub.task_type',
                'users.platform',
                DB::raw("COUNT(DISTINCT CASE WHEN cso_sub.order_status = 7 THEN cso_sub.id END) as pass_count"),
                DB::raw("SUM(IF(cso_sub.order_status = 7, cso_sub.amount, 0))                   as pass_amount"),
                DB::raw('COUNT(DISTINCT cso_sub.id)                                             as order_count'),
                DB::raw('SUM(cso_sub.amount)                                                    as order_amount'),
            ])
            ->whereNull('system_user.deleted_at')
            ->groupBy('system_user.id', 'cso_sub.task_type', 'users.platform');

        $paginate = SystemUser::query()
            ->whereHas('user', function ($query) {
                $query->userDataScope()->platformDataScope();
            })
            ->leftJoin('users', 'users.created_by', '=', 'system_user.id')
            ->leftJoin('crm_shop_order', 'crm_shop_order.user_id', '=', 'users.id')
            ->leftJoin('crm_call_record', function ($join) {
                $join->on('crm_call_record.user_id', '=', 'users.id')
                    ->on('crm_call_record.created_by', '=', 'system_user.id');
            })
            ->leftJoinSub($posSubQuery, 'pos', function ($join) {
                $join->on('system_user.id', '=', 'pos.system_user_id')
                    ->on('crm_shop_order.task_type', '=', 'pos.task_type')
                    ->on('users.platform', '=', 'pos.platform');
            })
            ->when(isset($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) === 2, function ($query) use ($params) {
                $query->whereBetween('users.created_at', [$params['created_at'][0], $params['created_at'][1]]);
            })
            ->when(isset($params['order_status']), function ($query) use ($params) {
                $query->where('crm_shop_order.order_status', $params['order_status']);
            })
            ->when(isset($params['task_type']), function ($query) use ($params) {
                $query->where('crm_shop_order.task_type', $params['task_type']);
            })
            ->when(isset($params['shop_id']), function ($query) use ($params) {
                $query->where('crm_shop_order.shop_id', $params['shop_id']);
            })
            ->when(isset($params['amount']) && is_array($params['amount']) && count($params['amount']) === 2, function ($query) use ($params) {
                $query->whereBetween('crm_shop_order.amount', [$params['amount'][0], $params['amount'][1]]);
            })
            ->when(isset($params['platform']), function ($query) use ($params) {
                $query->where('users.platform', $params['platform']);
            })
            ->when(isset($params['user_group']), function ($query) use ($params) {
                $query->where('system_user.user_group', $params['user_group']);
            })
            ->select([
                'system_user.id',
                'system_user.username',
                'system_user.nickname',
                'system_user.user_group',
                'users.platform',
                'crm_shop_order.task_type',
                DB::raw('COUNT(DISTINCT users.id) as user_count'),
                DB::raw('COUNT(DISTINCT CASE WHEN crm_call_record.id IS NOT NULL THEN users.id END) as contacted_user_count'),
                DB::raw('COALESCE(COUNT(DISTINCT users.id), 0) - COALESCE(COUNT(DISTINCT CASE WHEN crm_call_record.id IS NOT NULL THEN users.id END), 0) as uncontacted_user_count'),
                DB::raw('COALESCE(pos.order_count, 0) as order_count'),
                DB::raw('COALESCE(pos.order_amount, 0) as order_amount'),
                DB::raw('COALESCE(pos.pass_count, 0) as pass_count'),
                DB::raw('COALESCE(pos.pass_amount, 0) as pass_amount'),
                DB::raw('COALESCE(ROUND(COALESCE(pos.pass_count, 0) * 1.0 / NULLIF(COUNT(DISTINCT crm_shop_order.id), 0), 2), 0) as pass_rate'),
            ])
            ->groupBy('system_user.id', 'crm_shop_order.task_type', 'users.platform')
            ->orderBy('order_count', 'desc')
            ->paginate((int)$perPage, ['*'], 'page', (int)$page);
        return $this->mapper->setPaginate($paginate);
    }

    public function getConversionStaByGroup(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;

        $posSubQuery = SystemUser::query()
            ->leftJoin('users', 'users.created_by', '=', 'system_user.id')
            ->leftJoin('crm_shop_order as cso_sub', 'cso_sub.user_id', '=', 'users.id')
            ->select([
                'system_user.user_group',
                'cso_sub.task_type',
                'users.platform',
                DB::raw("COUNT(DISTINCT CASE WHEN cso_sub.order_status = 7 THEN cso_sub.id END) as pass_count"),
                DB::raw("SUM(IF(cso_sub.order_status = 7, cso_sub.amount, 0))                   as pass_amount"),
                DB::raw('COUNT(DISTINCT cso_sub.id)                                             as order_count'),
                DB::raw('SUM(cso_sub.amount)                                                    as order_amount'),
            ])
            ->whereNull('system_user.deleted_at')
            ->groupBy('system_user.user_group', 'cso_sub.task_type', 'users.platform');

        $paginate = SystemUser::query()
            ->whereHas('user', function ($query) {
                $query->userDataScope()->platformDataScope();
            })
            ->leftJoin('users', 'users.created_by', '=', 'system_user.id')
            ->leftJoin('crm_shop_order', 'crm_shop_order.user_id', '=', 'users.id')
            ->leftJoin('crm_call_record', function ($join) {
                $join->on('crm_call_record.user_id', '=', 'users.id')
                    ->on('crm_call_record.created_by', '=', 'system_user.id');
            })
            ->leftJoinSub($posSubQuery, 'pos', function ($join) {
                $join->on('system_user.user_group', '=', 'pos.user_group')
                    ->on('crm_shop_order.task_type', '=', 'pos.task_type')
                    ->on('users.platform', '=', 'pos.platform');
            })
            ->when(isset($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) === 2, function ($query) use ($params) {
                $query->whereBetween('users.created_at', [$params['created_at'][0], $params['created_at'][1]]);
            })
            ->when(isset($params['order_status']), function ($query) use ($params) {
                $query->where('crm_shop_order.order_status', $params['order_status']);
            })
            ->when(isset($params['task_type']), function ($query) use ($params) {
                $query->where('crm_shop_order.task_type', $params['task_type']);
            })
            ->when(isset($params['shop_id']), function ($query) use ($params) {
                $query->where('crm_shop_order.shop_id', $params['shop_id']);
            })
            ->when(isset($params['amount']) && is_array($params['amount']) && count($params['amount']) === 2, function ($query) use ($params) {
                $query->whereBetween('crm_shop_order.amount', [$params['amount'][0], $params['amount'][1]]);
            })
            ->when(isset($params['platform']), function ($query) use ($params) {
                $query->where('users.platform', $params['platform']);
            })
            ->when(isset($params['user_group']), function ($query) use ($params) {
                $query->where('system_user.user_group', $params['user_group']);
            })
            ->select([
                'system_user.user_group',
                'users.platform',
                'crm_shop_order.task_type',
                DB::raw('COUNT(DISTINCT users.id) as user_count'),
                DB::raw('COUNT(DISTINCT CASE WHEN crm_call_record.id IS NOT NULL THEN users.id END) as contacted_user_count'),
                DB::raw('COALESCE(COUNT(DISTINCT users.id), 0) - COALESCE(COUNT(DISTINCT CASE WHEN crm_call_record.id IS NOT NULL THEN users.id END), 0) as uncontacted_user_count'),
                DB::raw('COALESCE(pos.order_count, 0) as order_count'),
                DB::raw('COALESCE(pos.order_amount, 0) as order_amount'),
                DB::raw('COALESCE(pos.pass_count, 0) as pass_count'),
                DB::raw('COALESCE(pos.pass_amount, 0) as pass_amount'),
                DB::raw('COALESCE(ROUND(COALESCE(pos.pass_count, 0) * 1.0 / NULLIF(COUNT(DISTINCT crm_shop_order.id), 0), 2), 0) as pass_rate'),
            ])
            ->groupBy('system_user.user_group', 'crm_shop_order.task_type', 'users.platform')
            ->orderBy('order_count', 'desc')
            ->paginate((int)$perPage, ['*'], 'page', (int)$page);
        return $this->mapper->setPaginate($paginate);
    }

    public function getConversionStaByPlatform(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;

        $posSubQuery = SystemUser::query()
            ->leftJoin('users', 'users.created_by', '=', 'system_user.id')
            ->leftJoin('crm_shop_order as cso_sub', 'cso_sub.user_id', '=', 'users.id')
            ->select([
                'cso_sub.task_type',
                'users.platform',
                DB::raw("COUNT(DISTINCT CASE WHEN cso_sub.order_status = 7 THEN cso_sub.id END) as pass_count"),
                DB::raw("SUM(IF(cso_sub.order_status = 7, cso_sub.amount, 0))                   as pass_amount"),
                DB::raw('COUNT(DISTINCT cso_sub.id)                                             as order_count'),
                DB::raw('SUM(cso_sub.amount)                                                    as order_amount'),
            ])
            ->whereNull('system_user.deleted_at')
            ->groupBy('cso_sub.task_type', 'users.platform');

        $paginate = SystemUser::query()
            ->whereHas('user', function ($query) {
                $query->userDataScope()->platformDataScope();
            })
            ->leftJoin('users', 'users.created_by', '=', 'system_user.id')
            ->leftJoin('crm_shop_order', 'crm_shop_order.user_id', '=', 'users.id')
            ->leftJoin('crm_call_record', function ($join) {
                $join->on('crm_call_record.user_id', '=', 'users.id')
                    ->on('crm_call_record.created_by', '=', 'system_user.id');
            })
            ->leftJoinSub($posSubQuery, 'pos', function ($join) {
                $join->on('crm_shop_order.task_type', '=', 'pos.task_type')
                    ->on('users.platform', '=', 'pos.platform');
            })
            ->when(isset($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) === 2, function ($query) use ($params) {
                $query->whereBetween('users.created_at', [$params['created_at'][0], $params['created_at'][1]]);
            })
            ->when(isset($params['order_status']), function ($query) use ($params) {
                $query->where('crm_shop_order.order_status', $params['order_status']);
            })
            ->when(isset($params['task_type']), function ($query) use ($params) {
                $query->where('crm_shop_order.task_type', $params['task_type']);
            })
            ->when(isset($params['shop_id']), function ($query) use ($params) {
                $query->where('crm_shop_order.shop_id', $params['shop_id']);
            })
            ->when(isset($params['amount']) && is_array($params['amount']) && count($params['amount']) === 2, function ($query) use ($params) {
                $query->whereBetween('crm_shop_order.amount', [$params['amount'][0], $params['amount'][1]]);
            })
            ->when(isset($params['platform']), function ($query) use ($params) {
                $query->where('users.platform', $params['platform']);
            })
            ->select([
                'users.platform',
                'crm_shop_order.task_type',
                DB::raw('COUNT(DISTINCT users.id) as user_count'),
                DB::raw('COUNT(DISTINCT CASE WHEN crm_call_record.id IS NOT NULL THEN users.id END) as contacted_user_count'),
                DB::raw('COALESCE(COUNT(DISTINCT users.id), 0) - COALESCE(COUNT(DISTINCT CASE WHEN crm_call_record.id IS NOT NULL THEN users.id END), 0) as uncontacted_user_count'),
                DB::raw('COALESCE(MAX(pos.order_count), 0) as order_count'),
                DB::raw('COALESCE(MAX(pos.order_amount), 0) as order_amount'),
                DB::raw('COALESCE(MAX(pos.pass_count), 0) as pass_count'),
                DB::raw('COALESCE(MAX(pos.pass_amount), 0) as pass_amount'),
                DB::raw('COALESCE(ROUND(COALESCE(MAX(pos.pass_count), 0) * 1.0 / NULLIF(COUNT(DISTINCT crm_shop_order.id), 0), 2), 0) as pass_rate'),
            ])
            ->groupBy('crm_shop_order.task_type', 'users.platform')
            ->orderBy('order_count', 'desc')
            ->paginate((int)$perPage, ['*'], 'page', (int)$page);
        return $this->mapper->setPaginate($paginate);
    }
}
