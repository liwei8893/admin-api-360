<?php

declare(strict_types=1);

namespace App\Question\Service;

use App\Course\Model\CoursePeriod;
use App\Course\Service\CoursePeriodService;
use App\Question\Mapper\QuestionMapper;
use App\Question\Model\Question;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Collection;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\SubjectAuth;
use Mine\Exception\NormalStatusException;

class QuestionAppService extends AbstractService
{
    /**
     * @var QuestionMapper
     */
    #[Inject]
    public $mapper;

    #[Inject]
    public QuestionService $questionService;

    #[Inject]
    protected CoursePeriodService $coursePeriodService;

    #[Inject]
    protected DenseVolumeService $volumeService;

    /**
     * 题库中心列表.
     */
    public function getQuestionHomeList(array $params): array
    {
        // 每课优题
        if ((int) $params['type'] === 0) {
            $params = $this->handleData($params);
            $params['select'] = 'id,knows_id,classify_id,ques_type,ques_title,updated_at';
            $data = $this->mapper->getQuestionHomeList($params);
            // 添加封面 img
            $this->handleQuestionListImg($data, $params['subject'] ? (int) $params['subject'] : 0);
            $data['items'] = $this->questionWithCourse($data['items']);
            return $data;
        }
        // 试卷
        $params['select'] = 'id,name,updated_at';
        $params['orderBy'] = ['new_state', 'sort', 'name'];
        $params['orderType'] = ['desc', 'desc', 'desc'];
        $data = $this->volumeService->getPageList($params);
        // 添加封面 img
        $this->handleQuestionListImg($data, $params['subject'] ? (int) $params['subject'] : 0);
        return $data;
    }

    /**
     * 我的错题,错题搜藏,做题记录.
     */
    public function getUserQuestion(array $params): array
    {
        $pageData = $this->mapper->getUserQuestion($params);
        $items = [];
        foreach ($pageData['items'] as $item) {
            $items[] = $item->toArray();
        }
        $pageData['items'] = $this->handleGetData($items);
        return $pageData;
    }

    /**
     * 测一测,练一练.
     */
    #[SubjectAuth]
    public function getAppCourseQuestion(array $params): array
    {
        $userId = user('app')->getId();
        /* @var CoursePeriod $periodModel 用章节ID查询章节信息,获取题目ID,测一测ID */
        $periodModel = $this->coursePeriodService->read((int) $params['period_id']);
        if (! $periodModel) {
            throw new NormalStatusException('章节不存在!');
        }
        // 用课程ID查询课程信息,获取年级,科目认证
        $courseModel = $periodModel->courseBasis;
        if (! $courseModel) {
            throw new NormalStatusException('课程不存在!');
        }
        $grade = $courseModel->basisGrade->pluck('key')->toArray();
        // 1练一练,2测一测
        $data = [];
        $params['states'] = Question::STATUS_ENABLE;
        if ((int) $params['channel'] === 1) {
            $params['id'] = explode(',', $periodModel->qurstion_str ?? '');
            $params['orderBy'] = ['sort', 'id'];
            $params['orderType'] = ['desc', 'desc'];
            $data = $this->mapper->getListCollect($params);
        } elseif ((int) $params['channel'] === 2) {
            $data = $periodModel->questionPeriod()->where('states', $params['states'])
                ->orderBy('sort', 'desc')->orderBy('id', 'desc')->get();
        }
        if ($data) {
            $data = $data->load([
                'questionSubject:value,label',
                'questionType:value,label',
                'questionHistory' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }]);
            $data = $this->handleGetData($data->toArray());
        }
        // 用题目ID,测一测ID查询题目信息
        return ['data' => $data, 'grade' => $grade, 'subject' => $courseModel->subject_id];
    }

    /**
     * 获取单个题目,权限只验证科目.
     */
    #[SubjectAuth]
    public function readQuestion(int $id): array
    {
        /* @var Question $data */
        $data = $this->read($id);
        if (! $data) {
            return ['data' => []];
        }
        $classifyToSubjectEnum = [2 => 3, 3 => 4, 4 => 5, 6 => 15, 7 => 8];
        $subject = $classifyToSubjectEnum[$data->classify_id] ?? 0;
        $userId = user('app')->getId();
        $data = $data->load([
            'questionSubject:value,label',
            'questionType:value,label',
            'questionHistory' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }]);
        $data = $data->toArray();
        $this->questionService->handleQuestionEmptyNum($data);
        return ['data' => $data, 'grade' => [1, 2, 5, 7, 9, 11, 14, 13, 12, 51], 'subject' => $subject];
    }

    /**
     * 题目关联课程.
     */
    public function questionWithCourse(array $data): Collection
    {
        $dataItem = collect($data);
        $knowsId = $dataItem->pluck('knows_id')->unique()->toArray();
        $questionId = $dataItem->pluck('id')->unique()->toArray();
        $toCourse = $this->mapper->getToCourseList($knowsId)->whereIn('question_id', $questionId);
        $keyByToCourse = $toCourse->keyBy('question_id');
        foreach ($dataItem as $item) {
            $item['toCourse'] = $keyByToCourse[$item['id']] ?? null;
        }
        return $dataItem;
    }

    protected function questionListMap(int $subject): string
    {
        $imgEnum = [
            3 => 'https://oss.hgyk365.com/pcImg/question/subject/icon-语文.png',
            4 => 'https://oss.hgyk365.com/pcImg/question/subject/icon-数学.png',
            6 => 'https://oss.hgyk365.com/pcImg/question/subject/icon-英语.png',
            15 => 'https://oss.hgyk365.com/pcImg/question/subject/icon-物理.png',
            8 => 'https://oss.hgyk365.com/pcImg/question/subject/icon-化学.png',
            25 => 'https://oss.hgyk365.com/pcImg/question/subject/icon-生物.png',
            26 => 'https://oss.hgyk365.com/pcImg/question/subject/icon-地理.png',
            23 => 'https://oss.hgyk365.com/pcImg/question/subject/icon-政治.png',
            24 => 'https://oss.hgyk365.com/pcImg/question/subject/icon-历史.png',
            53 => 'https://oss.hgyk365.com/pcImg/question/subject/icon-文综.png',
            54 => 'https://oss.hgyk365.com/pcImg/question/subject/icon-理综.png',
        ];
        return $imgEnum[$subject] ?? '';
    }

    protected function handleQuestionListImg(array &$data, int $subject): void
    {
        if (! empty($data['items'])) {
            // 添加封面 img
            $img = $this->questionListMap($subject);
            foreach ($data['items'] as &$item) {
                $item['img'] = $img;
            }
        }
    }

    protected function handleGetData(array $data): array
    {
        foreach ($data as &$item) {
            $this->questionService->handleQuestionEmptyNum($item);
        }
        return $data;
    }

    protected function handleData($params)
    {
        if (isset($params['subject'])) {
            $classifyEnum = [3 => 2, 4 => 3, 6 => 4, 15 => 6, 8 => 7];
            $params['classify_id'] = $classifyEnum[$params['subject']] ?? 0;
        }
        if (! isset($params['orderBy'])) {
            $params['orderBy'] = ['sort', 'id'];
        }
        if (! isset($params['orderType'])) {
            $params['orderType'] = ['desc', 'desc'];
        }
        if (! isset($params['status'])) {
            $params['status'] = 1;
        }
        if (! isset($params['states'])) {
            $params['states'] = 0;
        }
        return $params;
    }
}
