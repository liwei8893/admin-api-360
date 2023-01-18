<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\CourseBasisMapper;
use App\Course\Model\CourseBasis;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

/**
 * 课时详情表服务类.
 */
class CourseBasisService extends AbstractService
{
    /**
     * @var CourseBasisMapper
     */
    #[Inject]
    public $mapper;

    public function getAppPageList(array $params): array
    {
        if (empty($params['select'])) {
            $params['select'] = CourseBasis::COMMON_FIELDS;
        } else {
            $params['select'] = explode(',', $params['select']);
        }
        $params['states'] = CourseBasis::STATUS_NORMAL;
        $params['is_del'] = 0;
        if (! isset($params['orderBy'])) {
            $params['orderBy'] = ['sort', 'id'];
        }
        if (! isset($params['orderType'])) {
            $params['orderType'] = ['desc', 'asc'];
        }
        return $this->mapper->getPageList($params, false);
    }

    public function getPageListByRecycle(?array $params = null, bool $isScope = true): array
    {
        $params['is_del'] = 1;
        return $this->mapper->getPageList($params, $isScope);
    }

    public function recovery(array $ids): bool
    {
        return ! empty($ids) && $this->mapper->disable($ids, 'is_del');
    }

    public function delete(array $ids): bool
    {
        return ! empty($ids) && $this->mapper->enable($ids, 'is_del');
    }

    /**
     * 批量更新.
     * @param mixed $data
     */
    public function batchUpdate($data): int
    {
        $ids = $data['ids'];
        unset($data['ids']);
        return $this->mapper->batchUpdate($ids, $data);
    }

    /**
     * 修改状态
     * @param mixed $id
     * @param mixed $statusValue
     */
    public function changeCourseStatus($id, $statusValue): bool
    {
        return $this->mapper->update($id, ['states' => $statusValue]);
    }
}
