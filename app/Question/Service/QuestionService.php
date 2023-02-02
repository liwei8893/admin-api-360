<?php

declare(strict_types=1);

namespace App\Question\Service;

use App\Course\Model\CoursePeriod;
use App\Course\Service\CoursePeriodService;
use App\Question\Mapper\QuestionMapper;
use Hyperf\Di\Annotation\Inject;
use JsonException;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\SubjectAuth;
use Mine\Exception\NormalStatusException;

/**
 * 题库管理服务类.
 */
class QuestionService extends AbstractService
{
    /**
     * @var QuestionMapper
     */
    public $mapper;

    #[Inject]
    protected CoursePeriodService $coursePeriodService;

    public function __construct(QuestionMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 获取课程对应的题目.
     */
    public function getCourseQuestion(array $params): array
    {
        return $this->mapper->getCourseQuestion($params);
    }

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
        if ($params['channel'] === '1') {
            $params['id'] = explode(',', $periodModel->qurstion_str);
            $params['orderBy'] = ['sort', 'id'];
            $params['orderType'] = ['desc', 'desc'];
            $data = $this->mapper->getListCollect($params);
        } elseif ($params['channel'] === '2') {
            $data = $periodModel->questionPeriod()
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

    public function getUserQuestion(array $params): array
    {
        $pageData = $this->mapper->getUserQuestion($params);
        $pageData['items'] = $this->handleGetData($pageData['items']);
        return $pageData;
    }

    /**
     * 处理填空题.
     */
    public function handleQuestionEmptyNum(array &$item): void
    {
        // 填空题处理
        if ($item['ques_type'] === 6) {
            // 填空的数量
            try {
                $get_empty_numb_arr = json_decode($item['ques_option'], true, 512, JSON_THROW_ON_ERROR);
                if (! empty($get_empty_numb_arr)) {
                    $get_empty_numb = count($get_empty_numb_arr);
                } else {
                    $get_empty_numb = 0;
                }
                $item['empty_nmb'] = $get_empty_numb;    // 填空题填空的个数
            } catch (JsonException $e) {
                $item['empty_nmb'] = 0;
            }
        }
    }

    public function handleGetData(array $data): array
    {
        foreach ($data as &$item) {
            $this->handleQuestionEmptyNum($item);
        }
        return $data;
    }

    /**
     * 新增数据.
     * @throws JsonException
     */
    public function save(array $data): int
    {
        return $this->mapper->save($this->handleSaveData($data));
    }

    /**
     * 更新一条数据.
     * @throws JsonException
     */
    public function update(int $id, array $data): bool
    {
        return $this->mapper->update($id, $this->handleSaveData($data));
    }

    /**
     * @throws JsonException
     */
    protected function handleSaveData(array $data): array
    {
        // "title": "单选题", "key": "1"
        // "title": "多选题", "key": "2"
        // "title": "判断题", "key": "4"
        if (in_array((int) $data['ques_type'], [1, 2, 4])) {
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
        if ($data['ques_type'] === 5) {
            $data['ques_option'] = null;
        }
        // "title": "填空题", "key": "6"
        if ($data['ques_type'] === 6) {
            // 处理答案内容
            foreach ($data['ques_option'] as &$option) {
                $option['content'] = strip_tags(htmlspecialchars_decode($option['content']), '<img><strong><em><span><br><sup><sub>');
            }
            unset($option);
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
