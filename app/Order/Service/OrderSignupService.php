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

namespace App\Order\Service;

use App\Course\Service\CourseService;
use App\Order\Mapper\OrderSignupMapper;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Helper\LoginUser;

/**
 * 订单管理服务类
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
     * 报名
     * @param array $collects
     * @return bool
     * @throws \Exception
     * author:ZQ
     * time:2022-08-26 16:23
     */
    #[Transaction]
    public function adminSave(array $collects): bool
    {
        foreach ($collects as $collect) {
            // 查报名的课程 是否已经报过
            $courseModels = $this->courseService->getCourseInfoByIds($collect['course_signup'], ['id', 'title', 'price']);
            // 筛选出没报过的
            $diffCourse = $this->filterCourseIsHave($collect['userId'], $courseModels);
            if ($diffCourse->isEmpty()){
                continue;
            }
            // 组装数据
            $comParam = [
                'user_id' => $collect['userId'],
                'pay_states' => 7,
                'created_id' => $this->loginUser->getId(),
                'created_name' => $this->loginUser->getUsername(),
                'audit_status' => 0,
                'indate' => $collect['day'],
                'money' => $collect['price'],
                'remark' => '后台报名',
            ];
            // 插入
            foreach ($diffCourse as $course) {
                $insertData = $this->handleInsertCourseData($comParam, $course);
                $orderModel = $this->mapper->saveModel($insertData);
                !empty($collect['subject']) && $orderModel->orderSubject()->sync($collect['subject']);
                !empty($collect['grade']) && $orderModel->orderGrade()->sync($collect['grade']);
            }
        }
        return true;
    }

    /**
     * 过滤已经报名的课程
     * @param $userId
     * @param \Hyperf\Database\Model\Collection $courseModels
     * @return \Hyperf\Database\Model\Collection|\Hyperf\Utils\Collection
     * author:ZQ
     * time:2022-08-26 15:49
     */
    public function filterCourseIsHave($userId, Collection $courseModels): Collection|\Hyperf\Utils\Collection
    {
        $courseIds = $courseModels->pluck('id');
        $orderModel = $this->mapper->getUserCourseInfo($userId, $courseIds);
        $haveCourseIds = $orderModel->pluck('shop_id');
        $diffCourseIds = $courseIds->diff($haveCourseIds);
        return $courseModels->whereIn('id', $diffCourseIds);
    }

    /**
     * 处理插入数据
     * @param $data
     * @param $course
     * @param string $orderNum
     * @return array
     * @throws \Exception
     * author:ZQ
     * time:2022-08-26 16:16
     */
    public function handleInsertCourseData($data, $course, string $orderNum = ''): array
    {
        $orderNumber = empty($orderNum) ? $this->getOrderSn() : $orderNum;
        return [
            'user_id' => $data['user_id'],
            'shop_id' => $course['id'],
            'shop_name' => $course['title'],
            'order_number' => $orderNumber,
            'pay_number' => $orderNumber,
            'shop_type' => $data['shop_type'] ?? 1,
            'pay_type' => $data['pay_type'] ?? 6,//支付类型，管理员赠送
            'pay_states' => $data['pay_states'] ?? 1,//看是否需要审核
            'audit_status' => $data['audit_status'] ?? 0,
            'order_price' => isset($data['money']) ? $data['money'] * 100 : $course['price'],
            'is_logistics' => 0,
            'created_id' => $data['created_id'] ?? 0,
            'created_name' => $data['created_name'] ?? 0,
            'indate' => $data['indate'] ?? 30,
            'actual_price' => $data['money'] ?? '',
            'activities' => $data['activities'] ?? '',
            'remark' => $data['remark'] ?? '',
            'is_vip' => $data['is_vip'] ?? 0,
        ];
    }

    /**
     * 获取唯一订单号
     * @return string
     * @throws \Exception
     * author:ZQ
     * time:2022-08-26 16:09
     */
    public function getOrderSn(): string
    {
        return $this->mapper->getOrderSn();
    }

}