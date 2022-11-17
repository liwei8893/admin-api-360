<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Course\Mapper;

use App\Course\Model\CourseBasis;
use App\Order\Model\Order;
use App\Users\Model\Users;
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
     * @param $data
     * @return array
     */
    public function getHistoryList($data): array
    {
        $query = Order::query()->where('status', '!=', 2)
            ->with(['users:id,user_name,old_platform,platform,mobile,remark', 'orderGrade', 'orderSubject'])
            ->whereHas('users', function (Builder $query) {
                $query->where('user_type', Users::USER_TYPE)->platformDataScope();
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
