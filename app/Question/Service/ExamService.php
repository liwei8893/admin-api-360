<?php

declare(strict_types=1);

namespace App\Question\Service;

use App\Order\Model\Order;
use App\Question\Mapper\ExamMapper;
use App\Question\Model\Exam;
use App\Question\Model\ExamClassify;
use App\Users\Model\User;
use App\Users\Service\UsersService;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Di\Annotation\Inject;
use JsonException;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 试卷表服务类.
 */
class ExamService extends AbstractService
{
    /**
     * @var ExamMapper
     */
    public $mapper;

    #[Inject]
    protected ExamClassifyService $classifyService;

    #[Inject]
    protected ContainerInterface $container;

    public function __construct(ExamMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 试卷权限验证.
     * @param int $classify_id
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function examAuth(int $classify_id): bool
    {
        // 拿到分类信息,映射出对应订单ID,60=小学=1501,61=中学=1502
        /* @var ExamClassify $classifyModel */
        $classifyModel = $this->classifyService->read($classify_id);
        if (!$classifyModel) {
            throw new NormalStatusException('分类不存在!');
        }
        $shopIdMap = [60 => 1501, 61 => 1502];
        $shopId = $shopIdMap[$classifyModel->grade] ?? 0;

        // 拿到用户模型
        $userId = user('app')->getId();
        $userService = $this->container->get(UsersService::class);
        /* @var User $userModel */
        $userModel = $userService->read($userId);
        if (!$userModel) {
            throw new NormalStatusException('未查询到用户!');
        }
        // 拿到所有订单,题目永久有效期
        $orderModel = $userModel->orders()->normalOrder()->where('shop_id', $shopId)->first();
        if ($orderModel) {
            return true;
        }
        $subjectId = $classifyModel->subject;
        $gradeIdMap = [60 => [1, 2, 5, 7, 9, 11], 61 => [14, 13, 12]];
        $gradeId = $gradeIdMap[$classifyModel->grade];
        // 新逻辑,先验证分科订单
        /* @var Collection | Order[] | null $userOrderModel */
        $userOrderModel = $userModel->orderCourse()->with(['orderGrade', 'orderSubject', 'course' => function (BelongsTo $builder) {
            $builder->with('basisGrade');
        }])->get();

        $flag = false;
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

//                var_dump($orderCourse->title);
                // 是否购买当前科目,不等于0表示需要验证
                $hasSubject = $orderCourse->subject_id === 0 || $orderCourse->subject_id === (int)$subjectId;
                if (!$hasSubject) {
                    continue; //科目不通过
                }
//                var_dump('课程科目通过');
//                var_dump($orderSubject->isNotEmpty());
//                var_dump($orderSubject->whereIn('key', $subjectId)->isEmpty());
//                var_dump($subjectId);
//                var_dump($orderSubject->toArray());
                // 课程科目通过之后还要检测订单科目是否限制
                if ($orderSubject->isNotEmpty() && $orderSubject->whereIn('key', $subjectId)->isEmpty()) {
                    continue; //科目不通过
                }
//                var_dump('订单科目通过');

                // 是否购买当前年级,不等于空表示需要验证
                $hasGrade = $orderCourseGrade->isEmpty() || $orderCourseGrade->whereIn('key', $gradeId)->isNotEmpty();
                if (!$hasGrade) {
                    continue; //年级不通过
                }
//                var_dump('课程年级通过');
                // 课程年级通过之后还要检测订单年级是否限制
                if ($orderGrade->isNotEmpty() && $orderGrade->whereIn('key', $gradeId)->isEmpty()) {
                    continue; //年级不通过
                }

                // 开始验证课程
                // 验证课程类型
                $orderCourseType = explode(',', $orderCourse->course_sub_title);
                $hasType = $orderCourse->course_sub_title === '' || in_array("69", $orderCourseType, true);
                if (!$hasType) {
                    continue; //类型不通过
                }
                $flag = true;
            }
        }
        if (!$flag) {
            throw new NormalStatusException('未购买当前题目,请联系课程顾问购买!');
        }
        return true;
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        $data['created_by'] = user()->getId();
        $data['updated_by'] = user()->getId();
        return parent::save($this->handleData($data));
    }

    /**
     * 更新一条数据.
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_by'] = user()->getId();
        return parent::update($id, $this->handleData($data));
    }

    public function changeSort(array $data): bool
    {
        /* @var Exam $model */
        $model = $this->read($data['id']);
        if (!$model) {
            return false;
        }
        $model->sort = $data['sort'];
        return $model->save();
    }

    /**
     * 获取试卷列表
     * @param array $params
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAppExamList(array $params): array
    {
        if (!isset($params['classify_id'])) {
            return [];
        }
        // 增加权限验证
        $this->examAuth((int)$params['classify_id']);
        $userId = user('app')->getId();
        $params['status'] = 1;
        $params['orderBy'] = ['sort', 'id'];
        $params['orderType'] = ['desc', 'desc'];
        $data = $this->getListCollect($params);
        $data->load(['examHistory' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }]);
        return $data->toArray();
    }

    /**
     * 自动组卷
     * @param array $params
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getExamAuto(array $params): array
    {
        // 增加权限验证
        $this->examAuth((int)$params['classify_id']);
        $classifyId = $this->classifyService->findChildren((int)$params['classify_id'], ['id'])->pluck('id')->toArray();
        $examCount = 6;
        $result = [];
        for ($i = 0; $i < $examCount; $i++) {
            $exam = $this->mapper->randomExam($classifyId);
            if ($exam) {
                $result[] = $exam->toArray();
            }
        }
        return $result;
    }


    public function getExamHistoryList(array $params): array
    {
        $params['user_id'] = user('app')->getId();
        return $this->mapper->getExamHistoryList($params);
    }

    /**
     * @throws JsonException
     */
    protected function handleData(array $data): array
    {
        // "title": "单选题", "key": "1"
        // "title": "多选题", "key": "2"
        // "title": "判断题", "key": "4"
        if (in_array((int)$data['ques_type'], [1, 2, 4])) {
            // 处理答案内容
            foreach ($data['ques_option'] as &$option) {
                $option['content'] = htmlspecialchars_decode($option['content']);
            }
            unset($option);
            // 处理答案选项
            $data['ques_option'] = json_encode($data['ques_option'], JSON_THROW_ON_ERROR);
        }
        // "title": "问答题", "key": "5"
        // 问答题处理,答案选项为空
        if ((int)$data['ques_type'] === 5) {
            $data['ques_option'] = null;
        }
        // "title": "填空题", "key": "6"
        if ((int)$data['ques_type'] === 6) {
            // 处理答案内容
            foreach ($data['ques_option'] as &$option) {
                $option['content'] = strip_tags(htmlspecialchars_decode($option['content']), '<img><strong><em><span><br><sup><sub>');
            }
            unset($option);
            // 获取填空数量
            $data['empty_nmb'] = count($data['ques_option']);
            // 处理答案选项
            $data['ques_option'] = json_encode($data['ques_option'], JSON_THROW_ON_ERROR);
        }

        // 通用处理
        // 处理题干
        $data['ques_stem'] = htmlspecialchars_decode($data['ques_stem']);
        // 处理文本题干
        $data['ques_stem_text'] = strip_tags($data['ques_stem']);
        // 处理答案解析
        $data['ques_analysis'] = htmlspecialchars_decode($data['ques_analysis']);

        return $data;
    }
}
