<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\TalkMapper;
use Mine\Abstracts\AbstractService;

/**
 * 讲一讲审核服务类.
 */
class TalkService extends AbstractService
{
    /**
     * @var TalkMapper
     */
    public $mapper;

    public function __construct(TalkMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
