<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Course\Service;

use App\Course\Mapper\CourseBasisMapper;
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
     * @param $data
     * @return int
     */
    public function batchUpdate($data): int
    {
        $ids = $data['ids'];
        unset($data['ids']);
        return $this->mapper->batchUpdate($ids, $data);
    }

    /**
     * 修改状态
     * @param $id
     * @param $statusValue
     * @return bool
     */
    public function changeCourseStatus($id, $statusValue): bool
    {
        return $this->mapper->update($id, ['states' => $statusValue]);
    }
}
