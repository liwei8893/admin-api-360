<?php
declare(strict_types=1);

namespace App\Ai\Service;


use App\Ai\Mapper\AiKnowsClassifyMapper;
use App\Ai\Model\AiKnowsClassify;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use function Hyperf\Collection\collect;

/**
 * 知识点分类服务类
 */
class AiKnowsClassifyService extends AbstractService
{
    /**
     * @var AiKnowsClassifyMapper
     */
    public $mapper;

    public function __construct(AiKnowsClassifyMapper $mapper)
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

    public function getAppTree(array $params): array
    {
        return $this->mapper->getAppTree($params);
    }

    public function getAppList(array $params): array
    {
        return $this->mapper->getList($params, false);
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
        /* @var AiKnowsClassify $curModel */
        $curModel = $this->read($id);
        // 如果更新年级或者科目,把下级所有分类的年级科目都更新
        if ((isset($data['grade']) && (int)$data['grade'] !== $curModel->grade) || (isset($data['subject']) && (int)$data['subject'] !== $curModel->subject)) {
            $this->mapper->updateChildren($id, ['grade' => $data['grade'] ?? null, 'subject' => $data['subject'] ?? null]);
        }
        return $this->mapper->update($id, $this->handleData($data));
    }

    /**
     * 处理数据
     * @param $data
     * @return array
     */
    protected function handleData($data): array
    {
        if (isset($data['id'], $data['parent_id']) && (int)$data['id'] === (int)$data['parent_id']) {
            throw new NormalStatusException('上级不能等于本级', 500);
        }
        $pid = $data['parent_id'] ?? 0;

        if ($pid === 0) {
            $data['level'] = $data['parent_id'] = '0';
            $data['layer'] = 1;
        } else {
            /* @var AiKnowsClassify $parentMod */
            $parentMod = $this->read($pid);
            if ($parentMod) {
                $data['level'] = $parentMod->level . ',' . $data['parent_id'];
                $data['layer'] = $parentMod->layer + 1;
            }
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
        if (count($ids)) foreach ($ids as $id) {
            if (!$this->checkChildrenExists((int)$id)) {
                $this->mapper->realDelete([$id]);
            } else {
                array_push($ctuIds, $id);
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

    /**
     * 查找所有子元素
     * @param int $id
     * @param array $select
     * @return Collection|array
     */
    public function findChildren(int $id, array $select = ['*']): Collection|array
    {
        return $this->mapper->findChildren($id, $select);
    }

    public function findAllChildren(array $ids, array $select = ['*']): \Hyperf\Collection\Collection
    {
        $data = collect();
        foreach ($ids as $id) {
            $data = $data->merge($this->findChildren($id, $select));
        }
        return $data;
    }
}
