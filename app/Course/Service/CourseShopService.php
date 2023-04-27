<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\CourseShopMapper;
use App\Course\Model\CourseShop;
use App\Order\Model\Order;
use App\Users\Model\User;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractService;
use Mine\MineModel;

/**
 * shop服务类.
 */
class CourseShopService extends AbstractService
{
    /**
     * @var CourseShopMapper
     */
    public $mapper;

    public function __construct(CourseShopMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getFirst(int $id): MineModel
    {
        $data = $this->mapper->read($id);
        $data?->load(['courseBasis']);
        return $data;
    }

    public function getAppList(array $params): Collection
    {
        /** @var Builder|Collection|CourseShop[] $data */
        $data = $this->getListCollect($params, false);
        // 如果要购买后显示,直接删除
        $isLogin = user('app')->hasLogin();

        $orderIds = new Collection();
        if ($isLogin) {
            $userId = user('app')->getId();
            /** @var Collection $orderIds */
            $orderIds = Order::query()->where('user_id', $userId)->normalOrder()->isNotExpire()->pluck('shop_id');
        }
        return $data->filter(function (CourseShop $item) use ($isLogin, $orderIds) {
            // show_rule: 0:购买之后才显示,1:总是显示
            // vip_auth: 2超级会员,3至尊会员
            // 没登录 总是显示
            if (! $isLogin) {
                return $item['show_rule'] === 1;
            }
            // 登录了,没课
            if ($item['show_rule'] !== 1 && $orderIds->isEmpty()) {
                return false;
            }
            // 会员认证
            if ($item['vip_auth'] === 2 && $orderIds->search(User::VIP_TYPE_SUPER) !== false) {
                return true;
            }
            if ($item['vip_auth'] === 3 && $orderIds->search(User::VIP_TYPE_SUPREME) !== false) {
                return true;
            }

            // 购买之后才显示
            if ($item['show_rule'] === 0) {
                // 循环课程包
                $filterCourse = $item->courseBasis->filter(fn ($course) => $orderIds->search($course['id']) !== false);
                // 循环完了之后如果课程包没课就把课程删掉
                if ($filterCourse->isEmpty()) {
                    return false;
                }
            }
            return true;
        });
    }
}
