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


use App\Course\Mapper\CourseBasisTypeMapper;
use Mine\Abstracts\AbstractService;

/**
 * 课程分类服务类
 */
class CourseBasisTypeService extends AbstractService
{
    /**
     * @var CourseBasisTypeMapper
     */
    public $mapper;

    public function __construct(CourseBasisTypeMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 获取树列表
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getTreeList(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = false;
        return $this->mapper->getTreeList($params, true, 'id', 'parent_id');
    }

    /**
     * 从回收站获取树列表
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getTreeListByRecycle(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = true;
        return $this->mapper->getTreeList($params, true, 'id', 'parent_id');
    }

    /**
     * 获取前端选择树
     * @return array
     */
    public function getSelectTree(): array
    {
        return $this->mapper->getSelectTree();
    }

    /**
     * 新增数据
     * @param array $data
     * @return int
     */
    public function save(array $data): int
    {
        return $this->mapper->save($this->handleData($data));
    }

    /**
     * 更新
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return $this->mapper->update($id, $this->handleData($data));
    }

    /**
     * 处理数据
     * @param $data
     * @return array
     */
    protected function handleData($data): array
    {
        if (is_array($data['parent_id']) && !empty($data['parent_id'])) {
            $data['parent_id'] = array_pop($data['parent_id']);
        }
        return $data;
    }

    /**
     * 真实删除数据，跳过存在子节点的数据
     * @return array
     */
    public function realDel(array $ids): ?array
    {
        // 存在子节点，跳过的数据
        $ctuIds = [];
        if (count($ids)) {
            foreach ($ids as $id) {
                if (!$this->checkChildrenExists((int)$id)) {
                    $this->mapper->realDelete([$id]);
                } else {
                    $ctuIds[] = $id;
                }
            }
        }
        return count($ctuIds) ? $this->mapper->getTreeName($ctuIds) : null;
    }

    /**
     * 检查子节点是否存在
     * @param int $id
     * @return bool
     */
    public function checkChildrenExists(int $id): bool
    {
        return $this->mapper->checkChildrenExists($id);
    }
}