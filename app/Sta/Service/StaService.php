<?php

declare(strict_types=1);

namespace App\Sta\Service;

use App\Order\Model\Order;
use App\Sta\Mapper\StaMapper;
use App\Users\Model\User;
use App\Users\Model\UserCourseRecord;
use Carbon\Carbon;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\MineCollection;
use Mine\MineModel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class StaService extends AbstractService
{
    /**
     * @var StaMapper
     */
    #[Inject]
    public $mapper;

    public function getCourseRecord(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $params['start_time'] = ! empty($params['created_at'][0]) ? strtotime($params['created_at'][0]) : Carbon::now()->startOfDay()->subDays(7)->timestamp;
        $params['end_time'] = ! empty($params['created_at'][1]) ? strtotime($params['created_at'][1]) + 86400 : Carbon::now()->endOfDay()->timestamp;
        $paginate = UserCourseRecord::with([
            'courseBasis:course_basis.id,course_basis.id as course_basis_id,course_basis.title',
            'coursePeriod:id,course_basis_id,title',
            'users:id,user_name,mobile,users.platform'])
            ->leftJoin('order', 'order.user_id', 'user_course_record.user_id')
            ->where('order.shop_id', User::VIP_TYPE_SUPER)
            ->where('order.pay_states', Order::PAY_SUCCESS)
            ->where('order.deleted_at', 0)
            ->where('order.created_at', '>=', $params['start_time'])
            ->where('order.created_at', '<=', $params['end_time'])
            // 用户表筛选
            ->whereHas('users', function (Builder $query) use ($params) {
                $query->when(! empty($params['mobile']), function (Builder $query) use ($params) {
                    $query->where('mobile', $params['mobile']);
                })
                    ->when(! empty($params['platform']), function (Builder $query) use ($params) {
                        $query->where('platform', $params['platform']);
                    })
                    ->where('user_type', User::USER_TYPE)
                    ->platformDataScope();
            })
            ->select([
                'user_course_record.*',
                'order.created_at',
                'order.indate',
            ])
            ->selectRaw('FROM_UNIXTIME(order.created_at ) as order_created_at')
            ->orderBy('order.user_id', 'desc')
            ->paginate((int) $perPage, ['*'], 'page', (int) $page);
        return $this->mapper->setPaginate($paginate);
    }

    /**
     * 听课记录导出.
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getCourseRecordExport(array $params, string $dto, string $filename): ResponseInterface
    {
        $params['pageSize'] = 10000;
        $data = $this->getCourseRecord($params);
        return (new MineCollection())->export($dto, $filename, $data['items']);
    }

    public function getHasCourseRecord(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $params['start_time'] = ! empty($params['created_at'][0]) ? strtotime($params['created_at'][0]) : Carbon::now()->startOfDay()->subDays(7)->timestamp;
        $params['end_time'] = ! empty($params['created_at'][1]) ? strtotime($params['created_at'][1]) + 86400 : Carbon::now()->endOfDay()->timestamp;
        $paginate = Order::query()
            ->leftJoin('users as u', 'u.id', '=', 'order.user_id')
            ->leftJoin('user_course_record as ucr', 'ucr.user_id', '=', 'u.id')
            ->leftJoin('attribute_detail as ad', 'ad.id', '=', 'u.grade_id')
            ->leftJoin(DB::raw('(SELECT users_id FROM users_log GROUP BY users_id) AS ul'), 'ul.users_id', '=', 'u.id')
            ->where('order.shop_id', User::VIP_TYPE_SUPER)
            ->where('order.pay_states', Order::PAY_SUCCESS)
            ->where('order.deleted_at', 0)
            ->where('order.created_at', '>=', $params['start_time'])
            ->where('order.created_at', '<=', $params['end_time'])
            // 用户表筛选
            ->whereHas('users', function (Builder $query) use ($params) {
                $query->when(! empty($params['mobile']), function (Builder $query) use ($params) {
                    $query->where('mobile', $params['mobile']);
                })
                    ->when(! empty($params['platform']), function (Builder $query) use ($params) {
                        $query->where('platform', $params['platform']);
                    })
                    ->where('user_type', User::USER_TYPE)
                    ->platformDataScope();
            })
            ->select([
                'u.id',
                'u.user_name',
                'mobile',
                'u.platform',
                DB::raw('COUNT(ucr.id) as has_record'),
                DB::raw('COUNT(ul.users_id) as has_login'),
                'order.created_at',
                'order.indate',
                'u.grade_id',
                'ad.detail_name as grade_name',
                DB::raw('sum(ucr.watch_time) AS watch_time_sum'),
                DB::raw('FROM_UNIXTIME(order.created_at ) as order_created_at'),
            ])
            ->groupBy(['u.id', 'ucr.user_id', 'order.created_at', 'order.indate'])
            ->orderBy('watch_time_sum', 'desc')
            ->paginate((int) $perPage, ['*'], 'page', (int) $page);
        return $this->mapper->setPaginate($paginate);
    }

    /**
     * 是否听课记录导出.
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getHasCourseRecordExport(array $params, string $dto, string $filename): ResponseInterface
    {
        $params['pageSize'] = 10000;
        $data = $this->getHasCourseRecord($params);
        $cb = function (&$item) {
            $item['has_record'] = ! empty($item['has_record']) ? '是' : '否';
            $item['has_login'] = ! empty($item['has_login']) ? '是' : '否';
            $item['watch_time_sum'] = round($item['watch_time_sum'] / 60);
        };
        return (new MineCollection())->export($dto, $filename, $data['items'], $cb);
    }

    public function getOrderAdd(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $params['start_time'] = ! empty($params['created_at'][0]) ? strtotime($params['created_at'][0]) : Carbon::now()->startOfDay()->subDays(7)->timestamp;
        $params['end_time'] = ! empty($params['created_at'][1]) ? strtotime($params['created_at'][1]) + 86400 : Carbon::now()->endOfDay()->timestamp;
        $paginate = Order::query()
            ->with(['orderGrade', 'orderSubject'])
            ->leftJoin('users as u', 'order.user_id', 'u.id')
            ->leftJoin('course_basis as b', 'b.id', 'order.shop_id')
            ->where('order.created_at', '>=', $params['start_time'])
            ->where('order.created_at', '<=', $params['end_time'])
            ->when(isset($params['actual_price']), static function (Builder $query) use ($params) {
                // 是否付款筛选
                if ($params['actual_price'] === '0') {
                    $query->where('order.actual_price', 0);
                }
                if (! empty($params['actual_price']) && $params['actual_price'] === '1') {
                    $query->where('order.actual_price', '!=', 0);
                }
            })
            ->when(! empty($params['vip_type']), static function (Builder $query) use ($params) {
                // 会员类型筛选,1优享会员,2超级会员,3至尊会员
                if ($params['vip_type'] === '2') {
                    $query->where('order.shop_id', User::VIP_TYPE_SUPER);
                }
            })
            // 用户表筛选
            ->whereHas('users', function (Builder $query) use ($params) {
                $query->when(! empty($params['mobile']), function (Builder $query) use ($params) {
                    $query->where('mobile', $params['mobile']);
                })
                    ->when(! empty($params['platform']), function (Builder $query) use ($params) {
                        $query->where('platform', $params['platform']);
                    })
                    ->where('user_type', User::USER_TYPE)
                    ->platformDataScope();
            })
            ->where('order.pay_states', Order::PAY_SUCCESS)
            ->where('order.deleted_at', 0)
            ->select([
                'order.id',
                'order.indate',
                'order.created_at',
                'order.shop_name',
                'order.remark as oRemark',
                'sale_platform',
                'u.platform',
                'u.user_name', 'u.mobile', 'u.status as userStatus',
                'u.remark as uRemark',
                'actual_price',
            ])
            ->selectRaw("from_unixtime(order.created_at,'%Y-%m-%d %h:%m:%s') as order_created_at")
            ->orderBy('order.created_at', 'DESC')
            ->paginate((int) $perPage, ['*'], 'page', (int) $page);
        return $this->mapper->setPaginate($paginate);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function getOrderAddExport(array $params, string $dto, string $filename): ResponseInterface
    {
        $params['pageSize'] = 10000;
        $data = $this->getOrderAdd($params);
        $cb = function (&$item) {
            $item['order_grade'] = $item['orderGrade']->implode('title', ',');
            $item['order_subject'] = $item['orderSubject']->implode('title', ',');
        };
        return (new MineCollection())->export($dto, $filename, $data['items'], $cb);
    }
}
