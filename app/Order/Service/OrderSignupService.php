<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Course\Service\CourseService;
use App\Order\Mapper\OrderSignupMapper;
use App\Order\Model\Order;
use App\Score\Event\ScoreAddEvent;
use Exception;
use Hyperf\Database\Model\Collection;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Helper\LoginUser;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 订单管理服务类.
 */
class OrderSignupService extends AbstractService
{
    /**
     * @var OrderSignupMapper
     */
    #[inject]
    public $mapper;

    #[Inject]
    protected LoginUser $loginUser;

    #[Inject]
    protected CourseService $courseService;

    /**
     * 报名.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Transaction]
    public function adminSave(array $collects): bool
    {
        foreach ($collects as $collect) {
            // 查报名的课程 是否已经报过
            $courseModels = $this->courseService->getCourseInfoByIds($collect['course_signup'], ['id', 'title', 'price']);
            // 筛选出没报过的
            $diffCourse = $this->filterCourseIsHave((int) $collect['userId'], $courseModels);
            if ($diffCourse->isEmpty()) {
                continue;
            }
            // 组装数据
            $comParam = [
                'user_id' => $collect['userId'],
                'indate' => $collect['day'],
                'money' => $collect['price'],
                'remark' => '后台报名',
            ];
            // 插入
            foreach ($diffCourse as $course) {
                $insertData = $this->handleInsertCourseData($comParam, $course);
                /* @var Order $orderModel */
                $orderModel = $this->mapper->saveModel($insertData);
                ! empty($collect['subject']) && $orderModel->orderSubject()->sync($collect['subject']);
                ! empty($collect['grade']) && $orderModel->orderGrade()->sync($collect['grade']);
                // TODO 新增会员时增加积分,只有报超级会员时才加积分,$insertData['pay_states]===7时增加,===8时在审核时增加
                if ($insertData['pay_states'] === Order::PAY_SUCCESS && $course['id'] === 950) {
                    event(new ScoreAddEvent('init', (int) $collect['userId'], $orderModel->id));
                }
            }
        }
        return true;
    }

    /**
     * 过滤已经报名的课程.
     */
    public function filterCourseIsHave(int $userId, Collection $courseModels): Collection|\Hyperf\Utils\Collection
    {
        $courseIds = $courseModels->pluck('id');
        $orderModel = $this->mapper->getUserCourseInfo($userId, $courseIds);
        $haveCourseIds = $orderModel->pluck('shop_id');
        $diffCourseIds = $courseIds->diff($haveCourseIds);
        return $courseModels->whereIn('id', $diffCourseIds);
    }

    /**
     * 处理插入数据.
     * @throws Exception
     */
    public function handleInsertCourseData(array $data, mixed $course, string $orderNum = ''): array
    {
        $orderNumber = empty($orderNum) ? $this->getOrderSn() : $orderNum;
        return [
            'user_id' => $data['user_id'],
            'shop_id' => $course['id'],
            'shop_name' => $course['title'],
            'order_number' => $orderNumber,
            'pay_number' => $orderNumber,
            'shop_type' => $data['shop_type'] ?? 1,
            'pay_type' => $data['pay_type'] ?? 6, // 支付类型，管理员赠送
            'pay_states' => $data['pay_states'] ?? $this->loginUser->isNoAuditRole() ? Order::PAY_SUCCESS : Order::PAY_AUDIT,
            'created_id' => $this->loginUser->getId(),
            'created_name' => $this->loginUser->getScene() === 'app' ? '' : $this->loginUser->getUsername(),
            'order_price' => isset($data['money']) ? $data['money'] * 100 : $course['price'],
            'is_logistics' => 0,
            'indate' => $data['indate'] ?? 30,
            'actual_price' => $data['money'] ?? '',
            'activities' => $data['activities'] ?? '',
            'remark' => $data['remark'] ?? '',
            'is_vip' => $data['is_vip'] ?? 0,
        ];
    }

    /**
     * 获取唯一订单号.
     * @throws Exception
     */
    public function getOrderSn(): string
    {
        return $this->mapper->getOrderSn();
    }
}
