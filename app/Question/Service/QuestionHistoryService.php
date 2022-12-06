<?php

declare(strict_types=1);

namespace App\Question\Service;

use App\Question\Mapper\QuestionHistoryMapper;
use Mine\Abstracts\AbstractService;

/**
 * 错题表服务类.
 */
class QuestionHistoryService extends AbstractService
{
    /**
     * @var QuestionHistoryMapper
     */
    public $mapper;

    public function __construct(QuestionHistoryMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
