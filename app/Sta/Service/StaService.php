<?php

declare(strict_types=1);

namespace App\Sta\Service;

use App\Course\Model\CourseBasis;
use App\Course\Model\CoursePeriod;
use App\Order\Model\Order;
use App\Order\Model\OrderSummary;
use App\Order\Model\OrderTransaction;
use App\Order\Model\UsersRenew;
use App\Sta\Mapper\StaMapper;
use App\Users\Model\User;
use App\Users\Model\UserCourseRecord;
use Carbon\Carbon;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\HasOne;
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

    public function getUsersTotal(array $params): array
    {
        $count = User::query()->whereHas('orders', function (Builder $query) use ($params) {
            $query->whereRaw('(created_at + (indate * 86400)) > unix_timestamp(now())')
                ->where('deleted_at', 0)
                ->where('status', 1)
                ->where('pay_states', 7)
                ->when(isset($params['start_time'], $params['end_time']), function (Builder $query) use ($params) {
                    $query->whereBetween('created_at', [$params['start_time'], $params['end_time']]);
                })
                ->vipOrder();
        })->count();
        return ['count' => $count];
    }

    public function getCourseHits(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $hits = $params['hits'] ?? 0;
        $paginate = CoursePeriod::with(['courseBasis:course_basis.id,course_basis.id as course_basis_id,course_basis.title'])
            ->select(['course_basis_id'])
            ->selectRaw('sum(real_hits) as hits')
            ->where('real_hits', '>', 0)
            // 课程筛选
            ->whereHas('courseBasis', function (Builder $query) use ($params) {
                $query->when(!empty($params['course_basis_title']), function (Builder $query) use ($params) {
                    $query->where('course_basis.title', 'like', "%{$params['course_basis_title']}%");
                });
            })
            ->groupBy('course_basis_id')->orderBy('hits', 'desc')
            ->having('hits', '>', $hits)
            ->paginate((int)$perPage, ['*'], 'page', (int)$page);
        return $this->mapper->setPaginate($paginate);
    }

    public function getCourseHitsDetail(int $id): array|Collection
    {
        return CoursePeriod::query()
            ->select(['id', 'course_basis_id', 'real_hits', 'title'])
            ->where('course_basis_id', $id)
            ->get();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function getCourseHitsExport(array $params, string $dto, string $filename): ResponseInterface
    {
        $params['pageSize'] = 100000;
        $data = $this->getCourseHits($params);
        return (new MineCollection())->export($dto, $filename, $data['items']);
    }

    public function getPeriodHits(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $hits = $params['real_hits'] ?? 0;
        $paginate = CoursePeriod::with(['courseBasis:course_basis.id,course_basis.id as course_basis_id,course_basis.title'])
            ->select(['id', 'course_basis_id', 'real_hits', 'title'])
            ->where('real_hits', '>', $hits)
            // 课程筛选
            ->whereHas('courseBasis', function (Builder $query) use ($params) {
                $query->when(!empty($params['course_basis_title']), function (Builder $query) use ($params) {
                    $query->where('course_basis.title', 'like', "%{$params['course_basis_title']}%");
                });
            })
            ->when(!empty($params['title']), function (Builder $query) use ($params) {
                $query->where('title', 'like', "%{$params['title']}%");
            })
            ->orderBy('real_hits', 'desc')
            ->paginate((int)$perPage, ['*'], 'page', (int)$page);
        return $this->mapper->setPaginate($paginate);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function getPeriodHitsExport(array $params, string $dto, string $filename): ResponseInterface
    {
        $params['pageSize'] = 100000;
        $data = $this->getPeriodHits($params);
        return (new MineCollection())->export($dto, $filename, $data['items']);
    }

    public function getCourseRecord(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $params['start_time'] = !empty($params['created_at'][0]) ? strtotime($params['created_at'][0]) : Carbon::now()->startOfDay()->subDays(7)->timestamp;
        $params['end_time'] = !empty($params['created_at'][1]) ? strtotime($params['created_at'][1]) + 86400 : Carbon::now()->endOfDay()->timestamp;

        $subQueryShopId = CourseBasis::query()->select('id')->where('course_title', 64);

        $paginate = UserCourseRecord::with([
            'courseBasis:course_basis.id,course_basis.id as course_basis_id,course_basis.title',
            'coursePeriod:id,course_basis_id,title',
            'users:id,user_name,mobile,users.platform'])
            ->leftJoin('order', 'order.user_id', 'user_course_record.user_id')
            ->whereIn('order.shop_id', $subQueryShopId)
            ->where('order.status', '!=', Order::STATUS_REFUND)
            ->where('order.pay_states', Order::PAY_SUCCESS)
            ->where('order.deleted_at', 0)
            ->where('order.created_at', '>=', $params['start_time'])
            ->where('order.created_at', '<=', $params['end_time'])
            // 课程筛选
            ->whereHas('courseBasis', function (Builder $query) use ($params) {
                $query->when(!empty($params['course_basis_title']), function (Builder $query) use ($params) {
                    $query->where('course_basis.title', 'like', "%{$params['course_basis_title']}%");
                });
            })
            // 用户表筛选
            ->whereHas('users', function (Builder $query) use ($params) {
                $query->when(!empty($params['mobile']), function (Builder $query) use ($params) {
                    $query->where('mobile', $params['mobile']);
                })
                    ->when(!empty($params['platform']), function (Builder $query) use ($params) {
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
            ->selectRaw('FROM_UNIXTIME(order.created_at) as order_created_at')
            ->orderBy('order.user_id', 'desc')
            ->paginate((int)$perPage, ['*'], 'page', (int)$page);
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
        $params['pageSize'] = 100000;
        $data = $this->getCourseRecord($params);
        return (new MineCollection())->export($dto, $filename, $data['items']);
    }

    public function getHasCourseRecord(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $params['start_time'] = !empty($params['created_at'][0]) ? strtotime($params['created_at'][0]) : Carbon::now()->startOfDay()->subDays(7)->timestamp;
        $params['end_time'] = !empty($params['created_at'][1]) ? strtotime($params['created_at'][1]) + 86400 : Carbon::now()->endOfDay()->timestamp;
        $paginate = Order::query()
            ->leftJoin('users as u', 'u.id', '=', 'order.user_id')
            ->leftJoin('user_course_record as ucr', 'ucr.user_id', '=', 'u.id')
            ->leftJoin('attribute_detail as ad', 'ad.id', '=', 'u.grade_id')
            ->leftJoin(DB::raw('(SELECT users_id FROM users_log GROUP BY users_id) AS ul'), 'ul.users_id', '=', 'u.id')
            ->vipOrder()
            ->where('order.status', '!=', Order::STATUS_REFUND)
            ->where('order.pay_states', Order::PAY_SUCCESS)
            ->where('order.deleted_at', 0)
            ->where('order.created_at', '>=', $params['start_time'])
            ->where('order.created_at', '<=', $params['end_time'])
            // 用户表筛选
            ->whereHas('users', function (Builder $query) use ($params) {
                $query->when(!empty($params['mobile']), function (Builder $query) use ($params) {
                    $query->where('mobile', $params['mobile']);
                })
                    ->when(!empty($params['platform']), function (Builder $query) use ($params) {
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
                DB::raw('FROM_UNIXTIME(order.created_at) as order_created_at'),
            ])
            ->groupBy(['u.id', 'ucr.user_id', 'order.created_at', 'order.indate'])
            ->orderBy('watch_time_sum', 'desc')
            ->paginate((int)$perPage, ['*'], 'page', (int)$page);
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
        $params['pageSize'] = 100000;
        $data = $this->getHasCourseRecord($params);
        $cb = function ($item) {
            $item['has_record'] = !empty($item['has_record']) ? '是' : '否';
            $item['has_login'] = !empty($item['has_login']) ? '是' : '否';
            $item['watch_time_sum'] = round($item['watch_time_sum'] / 60);
            return $item;
        };
        return (new MineCollection())->export($dto, $filename, $data['items'], $cb);
    }

    public function getOrderAdd(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $params['start_time'] = !empty($params['created_at'][0]) ? strtotime($params['created_at'][0]) : Carbon::now()->startOfMonth()->timestamp;
        $params['end_time'] = !empty($params['created_at'][1]) ? strtotime($params['created_at'][1]) + 86400 : Carbon::now()->endOfMonth()->timestamp;
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
                if (!empty($params['actual_price']) && $params['actual_price'] === '1') {
                    $query->where('order.actual_price', '!=', 0);
                }
            })
            ->when(!empty($params['vip_type']), static function (Builder $query) use ($params) {
                // 会员类型筛选,1优享会员,2超级会员,3至尊会员
                if ($params['vip_type'] === '2') {
                    $query->vipOrder();
                }
            })
            // 用户表筛选
            ->whereHas('users', function (Builder $query) use ($params) {
                $query->when(!empty($params['mobile']), function (Builder $query) use ($params) {
                    $query->where('mobile', $params['mobile']);
                })
                    ->when(!empty($params['platform']), function (Builder $query) use ($params) {
                        $query->where('platform', $params['platform']);
                    })
                    ->where('user_type', User::USER_TYPE)
                    ->platformDataScope();
            })
            ->where('order.pay_states', Order::PAY_SUCCESS)
            ->where('order.status', '!=', Order::STATUS_REFUND)
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
                'real_year',
            ])
            ->selectRaw('from_unixtime(order.created_at) as order_created_at')
            ->orderBy('order.created_at', 'DESC')
            ->paginate((int)$perPage, ['*'], 'page', (int)$page);
        return $this->mapper->setPaginate($paginate);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function getOrderAddExport(array $params, string $dto, string $filename): ResponseInterface
    {
        $params['pageSize'] = 100000;
        $data = $this->getOrderAdd($params);
        $cb = function ($item) {
            $item['order_grade'] = $item['orderGrade']->implode('title', ',');
            $item['order_subject_count'] = $item['orderSubject']->count();
            $item['order_subject'] = $item['orderSubject']->implode('title', ',');
            return $item;
        };
        return (new MineCollection())->export($dto, $filename, $data['items'], $cb);
    }

    public function getOrderRenew(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $params['start_time'] = !empty($params['created_at'][0]) ? strtotime($params['created_at'][0]) : Carbon::now()->startOfMonth()->timestamp;
        $params['end_time'] = !empty($params['created_at'][1]) ? strtotime($params['created_at'][1]) + 86400 : Carbon::now()->endOfMonth()->timestamp;
        $paginate = UsersRenew::query()
            ->with([
                'users:id,user_name,mobile,platform,remark',
                'order' => function (HasOne $query) {
                    $query->with(['orderGrade', 'orderSubject'])
                        ->select(['id', 'shop_name', 'remark', 'created_at', 'indate', 'shop_id']);
                },
            ])
            ->where('created_at', '>=', $params['start_time'])
            ->where('created_at', '<=', $params['end_time'])
            ->where('users_renew.audit_status', UsersRenew::AUDIT_SUCCESS)
            ->when(isset($params['status']), function (Builder $query) use ($params) {
                $query->where('status', $params['status']);
            })
            ->when(isset($params['money']), function (Builder $query) use ($params) {
                // 是否付款筛选
                if ($params['money'] === '0') {
                    $query->where('users_renew.money', 0);
                }
                if (!empty($params['money']) && $params['money'] === '1') {
                    $query->where('users_renew.money', '!=', 0);
                }
            })
            // 用户表筛选
            ->whereHas('users', function (Builder $query) use ($params) {
                $query->when(!empty($params['mobile']), function (Builder $query) use ($params) {
                    $query->where('mobile', $params['mobile']);
                })
                    ->when(!empty($params['platform']), function (Builder $query) use ($params) {
                        $query->where('platform', $params['platform']);
                    })
                    ->where('user_type', User::USER_TYPE)
                    ->platformDataScope();
            })
            // 订单表筛选
            ->whereHas('order', function (Builder $query) use ($params) {
                $query->when(!empty($params['vip_type']), static function (Builder $query) use ($params) {
                    // 会员类型筛选,1优享会员,2超级会员,3至尊会员
                    if ($params['vip_type'] === '2') {
                        $query->vipOrder();
                    }
                })
                    ->where('pay_states', Order::PAY_SUCCESS)
                    ->where('status', '!=', Order::STATUS_REFUND)
                    ->where('deleted_at', 0);
            })
            ->select([
                'user_id',
                'order_id',
                'indate_start',
                'indate_end',
                'created_at',
                'real_year',
                'money',
                'remark',
                'status',
            ])
            ->orderBy('users_renew.created_at', 'DESC')
            ->paginate((int)$perPage, ['*'], 'page', (int)$page);
        return $this->mapper->setPaginate($paginate);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function getOrderRenewExport(array $params, string $dto, string $filename): ResponseInterface
    {
        $params['pageSize'] = 100000;
        $data = $this->getOrderRenew($params);
        $cb = function ($item) {
            $item['order_grade'] = $item['order']['orderGrade']->implode('title', ',');
            $item['order_subject_count'] = $item['order']['orderSubject']->count();
            $item['order_subject'] = $item['order']['orderSubject']->implode('title', ',');
            $item = $item->toArray();
            $item['status'] = $item['status'] === 1 ? '续费' : '修改有效期';
            return $item;
        };
        return (new MineCollection())->export($dto, $filename, $data['items'], $cb);
    }

    public function getOrderRefund(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $params['start_time'] = !empty($params['created_at'][0]) ? $params['created_at'][0] : Carbon::now()->startOfMonth()->toDateTimeString();
        $params['end_time'] = !empty($params['created_at'][1]) ? Carbon::parse($params['created_at'][1])->endOfDay() : Carbon::now()->endOfMonth()->toDateTimeString();
        $paginate = OrderTransaction::query()
            ->with([
                'users:id,user_name,mobile,platform,remark',
                'order' => function (HasOne $query) {
                    $query->with(['orderGrade', 'orderSubject'])
                        ->select(['id', 'shop_name', 'remark', 'created_at', 'indate', 'shop_id', 'real_year', 'actual_price']);
                },
            ])
            ->where('create_at', '>=', $params['start_time'])
            ->where('create_at', '<=', $params['end_time'])
            ->where('type_id', 1)
            ->when(isset($params['money']), function (Builder $query) use ($params) {
                // 是否付款筛选
                if ($params['money'] === '0') {
                    $query->where('money', 0);
                }
                if (!empty($params['money']) && $params['money'] === '1') {
                    $query->where('money', '!=', 0);
                }
            })
            // 用户表筛选
            ->whereHas('users', function (Builder $query) use ($params) {
                $query->when(!empty($params['mobile']), function (Builder $query) use ($params) {
                    $query->where('mobile', $params['mobile']);
                })
                    ->when(!empty($params['platform']), function (Builder $query) use ($params) {
                        $query->where('platform', $params['platform']);
                    })
                    ->where('user_type', User::USER_TYPE)
                    ->platformDataScope();
            })
            // 订单表筛选
            ->whereHas('order', function (Builder $query) use ($params) {
                $query->when(!empty($params['vip_type']), static function (Builder $query) use ($params) {
                    // 会员类型筛选,1优享会员,2超级会员,3至尊会员
                    if ($params['vip_type'] === '2') {
                        $query->vipOrder();
                    }
                })
                    ->when(isset($params['actual_price']), static function (Builder $query) use ($params) {
                        // 是否付款筛选
                        if ($params['actual_price'] === '0') {
                            $query->where('order.actual_price', 0);
                        }
                        if (!empty($params['actual_price']) && $params['actual_price'] === '1') {
                            $query->where('order.actual_price', '!=', 0);
                        }
                    })
                    ->where('pay_states', Order::PAY_SUCCESS)
                    ->where('status', Order::STATUS_REFUND)
                    ->where('deleted_at', 0);
            })
            ->orderBy('create_at', 'DESC')
            ->paginate((int)$perPage, ['*'], 'page', (int)$page);
        return $this->mapper->setPaginate($paginate);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function getOrderRefundExport(array $params, string $dto, string $filename): ResponseInterface
    {
        $params['pageSize'] = 100000;
        $data = $this->getOrderRefund($params);
        $cb = function ($item) {
            $item['order_grade'] = $item['order']['orderGrade']->implode('title', ',');
            $item['order_subject'] = $item['order']['orderSubject']->implode('title', ',');
            $item['created_at'] = $item['order']['created_at']->toDateTimeString();
            return $item->toArray();
        };
        return (new MineCollection())->export($dto, $filename, $data['items'], $cb);
    }

    public function getOrderSummarySum(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $paginate = OrderSummary::query()
            ->when(isset($params['created_at'][0], $params['created_at'][1]), function (Builder $query) use ($params) {
                $query->whereBetween(
                    'created_at',
                    [strtotime($params['created_at'][0] . ' 00:00:00'), strtotime($params['created_at'][1] . ' 23:59:59')]
                );
            })
            ->when(isset($params['created_name']), function (Builder $query) use ($params) {
                $query->whereHas('adminUser', function (Builder $query) use ($params) {
                    $query->where('nickname', 'like', "%{$params['created_name']}%");
                });
            })
            ->select(['created_id'])
            ->selectRaw('count(*) as count')
            ->groupBy('created_id')
            ->with('adminUser:id,username,nickname')
            ->paginate((int)$perPage, ['*'], 'page', (int)$page);
        return $this->mapper->setPaginate($paginate);
    }

    /**
     * 核单数量导出.
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function getOrderSummarySumExport(array $params, string $dto, string $filename): ResponseInterface
    {
        $params['pageSize'] = 100000;
        $data = $this->getOrderSummarySum($params);
        $cb = function ($item) {
            $item['created_name'] = $item['adminUser']['nickname'];
            return $item->toArray();
        };
        return (new MineCollection())->export($dto, $filename, $data['items'], $cb);
    }

    /**
     * 做题数统计
     * @param array $params
     * @return array
     */
    public function getQuesCount(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $params['start_time'] = !empty($params['created_at'][0]) ? strtotime($params['created_at'][0]) : Carbon::now()->startOfDay()->subDays(7)->timestamp;
        $params['end_time'] = !empty($params['created_at'][1]) ? strtotime($params['created_at'][1]) + 86400 : Carbon::now()->endOfDay()->timestamp;
        $paginate = Order::query()
            ->leftJoin('users as u', 'u.id', '=', 'order.user_id')
            ->leftJoin('question_history as qh', 'qh.user_id', '=', 'u.id')
            ->leftJoin('attribute_detail as ad', 'ad.id', '=', 'u.grade_id')
            ->vipOrder()
            ->where('order.status', '!=', Order::STATUS_REFUND)
            ->where('order.pay_states', Order::PAY_SUCCESS)
            ->where('order.deleted_at', 0)
            ->where('order.created_at', '>=', $params['start_time'])
            ->where('order.created_at', '<=', $params['end_time'])
            // 用户表筛选
            ->whereHas('users', function (Builder $query) use ($params) {
                $query->when(!empty($params['mobile']), function (Builder $query) use ($params) {
                    $query->where('mobile', $params['mobile']);
                })
                    ->when(!empty($params['platform']), function (Builder $query) use ($params) {
                        $query->where('platform', $params['platform']);
                    })
                    ->where('user_type', User::USER_TYPE)
                    ->platformDataScope();
            })
            ->select([
                'u.id',
                'u.user_name',
                'u.mobile',
                'u.platform',
                'u.grade_id',
                DB::raw('COUNT(qh.user_id) as ques_count'),
                'order.created_at',
                'ad.detail_name as grade_name',
                DB::raw('FROM_UNIXTIME(order.created_at) as order_created_at'),
            ])
            ->groupBy(['u.id', 'order.created_at'])
            ->orderBy('ques_count', 'desc')
            ->paginate((int)$perPage, ['*'], 'page', (int)$page);
        return $this->mapper->setPaginate($paginate);
    }

    /**
     * 做题数统计导出
     * @param array $params
     * @param string $dto
     * @param string $filename
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function getQuesCountExport(array $params, string $dto, string $filename): ResponseInterface
    {
        $params['pageSize'] = 100000;
        $data = $this->getQuesCount($params);
        $cb = function ($item) {
            $item['has_record'] = !empty($item['has_record']) ? '是' : '否';
            $item['has_login'] = !empty($item['has_login']) ? '是' : '否';
            $item['watch_time_sum'] = round($item['watch_time_sum'] / 60);
            return $item;
        };
        return (new MineCollection())->export($dto, $filename, $data['items'], $cb);
    }

    /**
     * 续费预警
     * @param array $params
     * @return array
     */
    public function renewEarlyWarning(array $params): array
    {
        $perPage = $params['pageSize'] ?? MineModel::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $paginate = Order::query()
            ->leftJoin('users as u', 'u.id', '=', 'order.user_id')
            ->leftJoin('attribute_detail as ad', 'ad.id', '=', 'u.grade_id')
            ->leftJoin('course_basis as cb', 'cb.id', '=', 'order.shop_id')
            ->leftJoinSub('select users_id as id, count(*) as count
                    from users_log
                    group by users_id', 'll', 'll.id', '=', 'u.id')
            ->leftJoinSub('select user_id as id, count(*) as count, sum(watch_time) sum_time
                    from user_course_record
                    group by user_id', 't', 't.id', '=', 'u.id')
            ->where('cb.course_title', 64)
            ->where('order.pay_states', 7)
            ->where('order.deleted_at', 0)
            ->whereNotNull('u.id')
            ->where('u.user_type', 1)
            ->when(!empty($params['indate']) && is_array($params['indate']), function (Builder $query) use ($params) {
                $query->whereBetween('order.indate', [$params['indate'][0], $params['indate'][1]]);
            })
            ->when(!empty($params['mobile']), function (Builder $query) use ($params) {
                $query->where('u.mobile', $params['mobile']);
            })
            ->when(!empty($params['platform']), function (Builder $query) use ($params) {
                $query->where('u.platform', $params['platform']);
            })
            ->when(!empty($params['order_end_day_search']) && is_array($params['order_end_day_search']), function (Builder $query) use ($params) {
                $query->whereRaw('DATEDIFF(date_add(FROM_UNIXTIME(order.created_at), INTERVAL order.indate day), CURRENT_DATE()) between ? and ?', [$params['order_end_day_search'][0], $params['order_end_day_search'][1]]);
            })
            ->select([
                'order.id as order_id',
                'u.id as user_id',
                'u.user_name as user_name',
                'u.mobile as mobile',
                'u.platform as platform',
                't.count as course_record_count',
                't.sum_time as course_record_sum_time',
                DB::raw('ROUND(t.sum_time / 60 / 60, 2) as course_record_sum_time_hour'),
                'll.count as login_count',
                'order.indate as order_indate',
                'ad.detail_name as grade_name',
                DB::raw('FROM_UNIXTIME(u.created_at) as order_created_at'),
                DB::raw('date_add(FROM_UNIXTIME(order.created_at), INTERVAL order.indate day) as order_end_date'),
                DB::raw('DATEDIFF(date_add(FROM_UNIXTIME(order.created_at), INTERVAL order.indate day), CURRENT_DATE()) as order_end_day'),
            ])
            ->orderBy('order_end_day')
            ->paginate((int)$perPage, ['*'], 'page', (int)$page);
        return $this->mapper->setPaginate($paginate);
    }

    /**
     * 续费预警导出
     * @param array $params
     * @param string $dto
     * @param string $filename
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function renewEarlyWarningExport(array $params, string $dto, string $filename): ResponseInterface
    {
        $params['pageSize'] = 100000;
        $data = $this->renewEarlyWarning($params);
        $cb = function ($item) {
            return $item;
        };
        return (new MineCollection())->export($dto, $filename, $data['items'], $cb);
    }
}
