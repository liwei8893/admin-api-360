<?php

declare(strict_types=1);

namespace Mine\Aspect;

use App\Course\Model\CourseBasis;
use App\Course\Model\CoursePeriod;
use App\Course\Service\CourseChapterService;
use App\Course\Service\CoursePeriodService;
use App\Course\Service\CourseService;
use App\Order\Model\Order;
use App\Users\Model\User;
use App\Users\Service\UsersService;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Mine\Annotation\SubjectAuth;
use Mine\Exception\NormalStatusException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use function Hyperf\Collection\collect;

#[Aspect]
class SubjectAuthAspect extends AbstractAspect
{
    public array $annotations = [
        SubjectAuth::class,
    ];

    protected ContainerInterface $container;

    public function __construct()
    {
        $this->container = container();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint): mixed
    {
        // 切入方法执行 $data格式[url=>string,grade=>int,subject=>int]
        /**
         * @param string $data ['url']
         * @param int $data ['grade']
         * @param int $data ['subject']
         * @param int $data ['subject']
         */
        $data = $proceedingJoinPoint->process();

        // 获取注解参数
        /* @var SubjectAuth $auth */
        $auth = $proceedingJoinPoint->getAnnotationMetadata()->method[SubjectAuth::class];

        // 科目字段名称
        $subjectField = $auth->subjectField;
        $subjectId = $data[$subjectField];
        // 年级字段名称
        $gradeField = $auth->gradeField;
        $gradeId = $data[$gradeField];

        $courseField = $auth->courseField;
        $courseId = $data[$courseField] ?? null;

        $periodField = $auth->periodField;
        $periodId = $data[$periodField] ?? null;

        // 有章节ID检查是否免费章节,需要在验证用户之前
        if ($periodId) {
            $periodService = $this->container->get(CoursePeriodService::class);
            /* @var CoursePeriod $periodModel */
            $periodModel = $periodService->read($periodId);
            if ($periodModel && $periodModel->is_free === 1) {
                return $data;
            }
        }

        // 验证是否登录
        try {
            $userId = user('app')->getId();
        } catch (\Exception $e) {
            throw new NormalStatusException('未登录,请重新登录之后再执行操作!', 401);
        }
        $userService = $this->container->get(UsersService::class);
        /* @var User $userModel */
        $userModel = $userService->read($userId);
        if (!$userModel) {
            throw new NormalStatusException('未查询到用户!');
        }
        $courseService = $this->container->get(CourseService::class);

        // 如果有课程ID表示是验证课程权限,先验证课程是否单独购买
        $courseModel = null;
        if ($courseId) {
            /* @var CourseBasis $courseModel */
            $courseModel = $courseService->read($courseId);
            if (!$courseModel) {
                throw new NormalStatusException('课程不存在!');
            }
            $orderModel = $userModel->orders()->normalOrder()->isNotExpire()->where('shop_id', $courseId)->first();
            // 已经购买,直接通过
            if ($orderModel) {
                return $data;
            }
            // 课程需要购买,没购买
            if ($courseModel['is_give']) {
                throw new NormalStatusException('未购买当前课程,请联系课程顾问购买!');
            }
        }

        // 新逻辑,先验证分科订单
        /* @var Collection | Order[] | null $userOrderModel */
        $userOrderModel = $userModel->orderCourse()->with(['orderGrade', 'orderSubject', 'course' => function (BelongsTo $builder) {
            $builder->with('basisGrade');
        }])->get();
        // 如果有订单,需要验证
        if ($userOrderModel->isNotEmpty()) {
            // 循环订单,循环验证
            foreach ($userOrderModel as $item) {
                // 订单关联的课程
                $orderCourse = $item->course;
                // 订单关联课程的年级
                $orderCourseGrade = $orderCourse->basisGrade;
                // 订单年级
                $orderGrade = $item->orderGrade;
                // 订单科目
                $orderSubject = $item->orderSubject;
                // 是否购买当前科目,不等于0表示需要验证
                $hasSubject = $orderCourse->subject_id === 0 || $orderCourse->subject_id === (int)$subjectId;
                if (!$hasSubject) {
                    continue; //科目不通过
                }
                var_dump($orderCourse->title);
                var_dump($orderSubject->isNotEmpty());
                var_dump($orderSubject->whereIn('key', $subjectId)->isEmpty());
                // 课程科目通过之后还要检测订单科目是否限制
                if ($orderSubject->isNotEmpty() && $orderSubject->whereIn('key', $subjectId)->isEmpty()) {
                    continue; //科目不通过
                }

                // 是否购买当前年级,不等于空表示需要验证
                $hasGrade = $orderCourseGrade->isEmpty() || $orderCourseGrade->whereIn('key', $gradeId)->isNotEmpty();
                if (!$hasGrade) {
                    continue; //年级不通过
                }
                // 课程年级通过之后还要检测订单年级是否限制
                if ($orderGrade->isNotEmpty() && $orderGrade->whereIn('key', $gradeId)->isEmpty()) {
                    continue; //年级不通过
                }

                // 没有课程ID表示验证题目,题目只需要验证年级跟科目
                if (!$courseId) {
                    return $data;
                }
                // 类型守卫
                if (!$courseModel) {
                    continue;
                }

                // 开始验证课程
                // 是否验证季节,不等于0表示需要验证
                $hasSeason = $orderCourse->season === 0 || $orderCourse->season === $courseModel->season;
                if (!$hasSeason) {
                    continue; //季节不通过
                }

                // 验证课程类型
                $orderCourseType = explode(',', $orderCourse->course_sub_title);
                $hasType = $orderCourse->course_sub_title === '' || in_array((string)$courseModel->course_title, $orderCourseType, true);
                if (!$hasType) {
                    continue; //类型不通过
                }
                // 没有$periodId不验证章节
                if (!$periodId) {
                    return $data;
                }
                // 课程购买之后还需要验证章节权限,order.chapter_count_auth,代表可以观看前多少节课,0代表不限制
                if ($this->chapterCountAuth($item->chapter_count_auth, $courseModel->id, $periodId)) {
                    return $data;
                }
            }
        }
//
//        // 兼容之前老会员950
//        $user = $userModel->vipType()->with(['orderGrade', 'orderSubject'])->first();
//        // 没有老会员
//        if (!$user) {
//            $this->noPermissionTip();
//        }
//        // 订单年级,为空表示购买所有年级,直接通过
//        /* @var Collection $userGrade */
//        $userGrade = $user->orderGrade;
//        if ($userGrade->isEmpty()) {
//            return $data;
//        }
//        // 不为空表示购买部分年级,验证课程年级是否包含在内,不包含说明没有购买当前年级,不通过
//        $diffGrade = $userGrade->whereIn('key', $gradeId);
//        if ($diffGrade->isEmpty()) {
//            $this->noPermissionTip();
//        }
//        // 包含在内,表示购买了当前年级,在验证科目
//        /* @var Collection $userSubject */
//        $userSubject = $user->orderSubject;
//        // 没有用户科目表示只限制年级,通过
//        if ($userSubject->isEmpty()) {
//            return $data;
//        }
//        // 有科目表示还限制了科目,验证科目是否包含在内,不包含说明没有购买当前科目,不通过
//        $diffSubject = $userSubject->whereIn('key', $subjectId);
//        if ($diffSubject->isEmpty()) {
//            $this->noPermissionTip();
//        }
//        // 科目验证完毕,通过
//        return $data;
        $this->noPermissionTip();
    }

    protected function noPermissionTip(): void
    {
        throw new NormalStatusException('未购买当前科目,请联系课程顾问购买!');
    }

    /**
     * 验证章节权限,order.chapter_count_auth,代表可以观看前多少节课,0代表不限制
     * @param int $chapterCount
     * @param int $courseId
     * @param int $periodId
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function chapterCountAuth(int $chapterCount, int $courseId, int $periodId): bool
    {
        if ($chapterCount === 0) {
            return true;
        }
        // 找到观看的章节是第多少节,如果在购买的节数里就返回数据
        $chapterService = $this->container->get(CourseChapterService::class);
        $chapterData = $chapterService->getChapter($courseId);
        $periodData = [];
        foreach ($chapterData as $chapterItem) {
            foreach ($chapterItem['children'] as $child) {
                $periodData[] = $child['course_period']['id'];
            }
        }
        $index = collect($periodData)->search($periodId);
        return ($index + 1) <= $chapterCount;
    }
}
