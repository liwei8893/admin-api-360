<?php

declare(strict_types=1);

namespace App\Question\Service;

use App\Question\Mapper\QuestionMapper;
use JsonException;
use Mine\Abstracts\AbstractService;

/**
 * 题库管理服务类.
 */
class QuestionService extends AbstractService
{
    /**
     * @var QuestionMapper
     */
    public $mapper;

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

    /**
     * 新增数据.
     * @param array $data
     * @return int
     * @throws JsonException
     */
    public function save(array $data): int
    {
        return $this->mapper->save($this->handleData($data));
    }

    /**
     * 更新一条数据.
     * @param int $id
     * @param array $data
     * @return bool
     * @throws JsonException
     */
    public function update(int $id, array $data): bool
    {
        return $this->mapper->update($id, $this->handleData($data));
    }

    /**
     * @throws JsonException
     */
    protected function handleData(array $data): array
    {
        // "title": "单选题", "key": "1"
        // "title": "多选题", "key": "2"
        // "title": "判断题", "key": "4"
        if (in_array($data['ques_type'], ['1', '2', '4'], true)) {
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
        if ($data['ques_type'] === '5') {
            $data['ques_option'] = null;
        }
        // "title": "填空题", "key": "6"
        if ($data['ques_type'] === '6') {
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
