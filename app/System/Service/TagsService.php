<?php

declare(strict_types=1);

namespace App\System\Service;

use App\System\Mapper\TagsMapper;
use Hyperf\Cache\Annotation\Cacheable;
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

    #[Cacheable(prefix: 'Tags', value: 'AppTagList', ttl: 86400)]
    public function getTagList(array $params): array
    {
        $params['select'] = 'id,name';
        $params['status'] = 1;
        $params['hasCoursePeriod'] = true;
        return $this->getList($params, false);
    }
}
