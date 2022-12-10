<?php

declare(strict_types=1);

namespace App\Question\Service;

use App\Question\Mapper\QuestionMapper;
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
}
