<?php
declare(strict_types=1);


namespace App\Play\Service;

use App\Play\Mapper\PlayUserRecordMapper;
use App\Play\Model\PlayUserRecord;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractService;

/**
 * 用户游戏记录服务类
 */
class PlayUserRecordService extends AbstractService
{
    /**
     * @var PlayUserRecordMapper
     */
    public $mapper;

    public function __construct(PlayUserRecordMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function readByUserId(int $userId): array
    {
        return $this->mapper->readByUserId($userId)?->toArray() ?? [];
    }

    public function saveOrUpdate(array $params): Model|PlayUserRecord|Builder
    {
        $params['user_id'] = user('app')->getId();
        return $this->mapper->saveOrUpdate($params);
    }
}
