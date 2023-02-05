<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\TalkMapper;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;

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

    public function getAppPageList(array $params): array
    {
        $params = $this->handleData($params);
        return $this->getPageList($params);
    }

    public function vote($params): array
    {
        return $this->mapper->voteToggle($params['id'], user('app')->getId());
    }

    public function delete(array $ids): bool
    {
        foreach ($ids as $id) {
            // 判断是否是自己的内容,用户只能删除自己发布的
            $sunModel = $this->mapper->read($id);
            if (! $sunModel) {
                throw new NormalStatusException('内容不存在!');
            }
            if (user('app')->getId() !== $sunModel['user_id']) {
                throw new NormalStatusException('只能删除自己发布的内容!');
            }
        }
        return parent::delete($ids);
    }

    protected function handleData(array $params): array
    {
        if (! isset($params['status'])) {
            $params['status'] = 1;
        }
        if (! isset($params['withUser'])) {
            $params['withUser'] = true;
        }
        if (! isset($params['withUserVoteCount'])) {
            $params['withUserVoteCount'] = true;
        }
        if (! isset($params['orderBy'])) {
            $params['orderBy'] = ['user_vote_count', 'id'];
        }
        if (! isset($params['orderType'])) {
            $params['orderType'] = ['desc', 'desc'];
        }
        if (! isset($params['withUserNoAudit'])) {
            $params['withUserNoAudit'] = true;
        }
        return $params;
    }
}
