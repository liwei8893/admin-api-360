<?php
declare(strict_types=1);

namespace App\Ai\Service;

use App\Ai\Mapper\AiQuestionMapper;
use Hyperf\Di\Annotation\Inject;
use JsonException;
use Mine\Abstracts\AbstractService;
use Psr\Container\ContainerInterface;

/**
 * 题目管理服务类
 */
class AiQuestionService extends AbstractService
{
    /**
     * @var AiQuestionMapper
     */
    public $mapper;

    #[Inject]
    protected AiKnowsClassifyService $classifyService;

    #[Inject]
    protected ContainerInterface $container;

    public function __construct(AiQuestionMapper $mapper)
    {
        $this->mapper = $mapper;
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
        $model = $this->read($data['id']);
        if (!$model) {
            return false;
        }
        $model->sort = $data['sort'];
        return $model->save();
    }

    /**
     * 自动组卷
     * @param array $params
     * @return array
     */
    public function getExamAuto(array $params): array
    {
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
