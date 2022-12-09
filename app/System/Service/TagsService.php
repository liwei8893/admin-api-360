<?php

declare(strict_types=1);

namespace App\System\Service;

use App\System\Mapper\TagsMapper;
use Mine\Abstracts\AbstractService;

/**
 * 标签管理服务类.
 */
class TagsService extends AbstractService
{
    /**
     * @var TagsMapper
     */
    public $mapper;

    public function __construct(TagsMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
