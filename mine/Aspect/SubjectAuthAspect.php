<?php

declare(strict_types=1);

namespace Mine\Aspect;

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

        // 如果有课程ID先验证课程是否单独购买
        if ($courseId) {
            $courseService = $this->container->get(CourseService::class);
            $courseModel = $courseService->read($courseId);
            if (!$courseModel) {
                throw new NormalStatusException('课程不存在!');
            }
            // 新需求:番茄的课程有效期为永久,删除掉有效期查询判断->isNotExpire()
            $tomatoCourseId = [1489, 1490, 1491, 1492, 1493, 1494, 1495, 1496, 1497, 1498, 1499, 1500];
            if (in_array((int)$courseId, $tomatoCourseId, true)) {
                /* @var Order $orderModel */
                $orderModel = $userModel->orders()->normalOrder()->where('shop_id', $courseId)->first();
            } else {
                $orderModel = $userModel->orders()->normalOrder()->isNotExpire()->where('shop_id', $courseId)->first();
            }
            // 课程需要购买,没购买
            if (!$orderModel && $courseModel['is_give']) {
                throw new NormalStatusException('未购买当前课程,请联系课程顾问购买!');
            }
            if ($orderModel) {
                // 课程购买之后还需要验证章节权限,order.chapter_count_auth,代表可以观看前多少节课,0代表不限制
                $chapterCount = $orderModel->chapter_count_auth;
                if ($chapterCount === 0) {
                    return $data;
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
                if (($index + 1) <= $chapterCount) {
                    return $data;
                }
            }
            // 课程需要购买,没购买
            if ($courseModel['is_give']) {
                throw new NormalStatusException('未购买当前课程,请联系课程顾问购买!');
            }
        }

        // 新逻辑,先验证分科订单
        /** @var Collection $userSubjectOrder */
        $userSubjectOrder = $userModel->haveSubject()->with(['course' => function (BelongsTo $builder) {
            $builder->with('basisGrade')->select(['id', 'subject_id']);
        }])->get();
        // 有分科订单,科目和年级都验证通过就返回,不然往下走进行老950会员验证
        if ($userSubjectOrder->isNotEmpty()) {
            foreach ($userSubjectOrder as $item) {
                // 是否购买当前科目
                $hasSubject = (int)$item->course->subject_id === (int)$subjectId;
                // 是否购买当前年级
                $hasGrade = $item['course']['basisGrade']->whereIn('key', $gradeId);
                // 科目和年级都验证通过,表示购买了对应分科
                if ($hasSubject && $hasGrade->isNotEmpty()) {
                    return $data;
                }
            }
        }

        // 兼容之前老会员950
        $user = $userModel->vipType()->with(['orderGrade', 'orderSubject'])->first();
        // 没有老会员
        if (!$user) {
            $this->noPermissionTip();
        }
        // 订单年级,为空表示购买所有年级,直接通过
        /* @var Collection $userGrade */
        $userGrade = $user->orderGrade;
        if ($userGrade->isEmpty()) {
            return $data;
        }
        // 不为空表示购买部分年级,验证课程年级是否包含在内,不包含说明没有购买当前年级,不通过
        $diffGrade = $userGrade->whereIn('key', $gradeId);
        if ($diffGrade->isEmpty()) {
            $this->noPermissionTip();
        }
        // 包含在内,表示购买了当前年级,在验证科目
        /* @var Collection $userSubject */
        $userSubject = $user->orderSubject;
        // 没有用户科目表示只限制年级,通过
        if ($userSubject->isEmpty()) {
            return $data;
        }
        // 有科目表示还限制了科目,验证科目是否包含在内,不包含说明没有购买当前科目,不通过
        $diffSubject = $userSubject->whereIn('key', $subjectId);
        if ($diffSubject->isEmpty()) {
            $this->noPermissionTip();
        }
        // 科目验证完毕,通过
        return $data;
    }

    protected function noPermissionTip(): void
    {
        throw new NormalStatusException('未购买当前科目,请联系课程顾问购买!');
    }
}
