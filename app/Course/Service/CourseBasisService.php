<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\Course\Service;

use App\Course\Mapper\CourseBasisMapper;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

/**
 * 课时详情表服务类
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
        return !empty($ids) && $this->mapper->disable($ids, 'is_del');
    }

    public function delete(array $ids): bool
    {
        return !empty($ids) && $this->mapper->enable($ids, 'is_del');
    }

    /**
     * 批量更新
     * @param $data
     * @return int
     * author:ZQ
     * time:2022-08-21 17:56
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
     * author:ZQ
     * time:2022-08-22 10:45
     */
    public function changeCourseStatus($id, $statusValue): bool
    {
        return $this->mapper->update($id, ['states' => $statusValue]);
    }

}