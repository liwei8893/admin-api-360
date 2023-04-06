<?php

declare(strict_types=1);

namespace App\Course\Mapper;

use App\Course\Model\CourseBasis;
use App\Order\Model\Order;
use App\Users\Model\User;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

class CourseHistoryMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = CourseBasis::class;
    }

    /**
     * 课程购买记录.
     */
    public function getHistoryList(array $data): array
    {
        $query = Order::query()->where('status', '!=', Order::STATUS_REFUND)
            ->with(['users:id,user_name,old_platform,platform,mobile,remark', 'orderGrade', 'orderSubject'])
            ->whereHas('users', function (Builder $query) use ($data) {
                $query->where('user_type', User::USER_TYPE)
                    ->when(! empty($data['users_mobile']) && is_array($data['users_mobile']), function (Builder $query) use ($data) {
                        $query->whereIn('mobile', $data['users_mobile']);
                    })
                    ->when(! empty($data['users_mobile']) && ! is_array($data['users_mobile']), function (Builder $query) use ($data) {
                        $query->where('mobile', $data['users_mobile']);
                    })
                    ->when(! empty($data['users_platform']), function (Builder $query) use ($data) {
                        $query->where('platform', $data['users_platform']);
                    })
                    ->platformDataScope();
            })
            ->when(isset($data['created_at'][0], $data['created_at'][1]), function (Builder $query) use ($data) {
                $query->whereBetween(
                    'created_at',
                    [strtotime($data['created_at'][0]), strtotime($data['created_at'][1]) + 86400]
                );
            })
            ->where('shop_id', $data['shop_id'])
            ->orderBy('created_at', 'desc')
            ->noDeleteOrder();
        $perPage = $data['pageSize'] ?? $this->model::PAGE_SIZE;
        $page = $data['page'] ?? 1;
        $query = $query->paginate(
            (int) $perPage,
            ['*'],
            'page',
            (int) $page
        );
        return $this->setPaginate($query);
    }
}
