<?php

declare(strict_types=1);

namespace Mine\Aspect;

use App\Course\Service\CourseService;
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

        $userId = user('app')->getId();
        if (! $userId) {
            throw new NormalStatusException('未登录,请重新登录之后再执行操作!');
        }
        $userService = $this->container->get(UsersService::class);
        /* @var User $userModel */
        $userModel = $userService->read($userId);
        if (! $userModel) {
            throw new NormalStatusException('未查询到用户!');
        }
        // 如果有课程ID先验证课程是否单独购买
        if ($courseId) {
            $courseService = $this->container->get(CourseService::class);
            $courseModel = $courseService->read($courseId);
            if (! $courseModel) {
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
        $user = $userModel->vipType()->with(['orderGrade', 'orderSubject'])
            ->first();
        // 为空验证新分科权限
        if (! $user) {
            // 没会员就进行新分科验证
            /** @var Collection $userSubjectOrder */
            $userSubjectOrder = $userModel->haveSubject()->with(['course' => function (BelongsTo $builder) {
                $builder->with('basisGrade')->select(['id', 'subject_id']);
            }])->get();
            if ($userSubjectOrder->isEmpty()) {
                $this->noPermissionTip();
            }
            // 所有科目ID
            $allSubject = $userSubjectOrder->pluck('course.subject_id')->map(fn ($item) => ['key' => $item]);
            // 为空表示没有购买科目
            if ($allSubject->isEmpty()) {
                $this->noPermissionTip();
            }
            // 有科目,判断当前课程科目是否在内
            $diffSubject = $allSubject->whereIn('key', $subjectId);
            if ($diffSubject->isEmpty()) {
                $this->noPermissionTip();
            }
            // 判断年级,所有年级ID
            $allGrade = $userSubjectOrder->map(fn ($item) => $item['course']['basisGrade']->pluck('key'))->flatten()->unique()->values()->map(fn ($item) => ['key' => $item]);
            // 为空表示没有购买年级
            if ($allGrade->isEmpty()) {
                $this->noPermissionTip();
            }
            // 有科目,判断当前课程年级是否在内
            $diffGrade = $allGrade->whereIn('key', $gradeId);
            if ($diffGrade->isEmpty()) {
                $this->noPermissionTip();
            }
            return $data;
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
