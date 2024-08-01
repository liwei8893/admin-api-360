<?php

declare(strict_types=1);

namespace App\Question\Service;

use App\Question\Mapper\ExamClassifyMapper;
use App\Question\Model\ExamClassify;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;

/**
 * 试卷分类服务类.
 */
class ExamClassifyService extends AbstractService
{
    /**
     * @var ExamClassifyMapper
     */
    public $mapper;

    public function __construct(ExamClassifyMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 获取树列表.
     */
    public function getTreeList(?array $params = null, bool $isScope = false): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = false;
        return $this->mapper->getTreeList($params, true, 'id', 'parent_id');
    }

    /**
     * 从回收站获取树列表.
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
     * 获取前端选择树.
     */
    public function getSelectTree(): array
    {
        return $this->mapper->getSelectTree();
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        return $this->mapper->save($this->handleData($data));
    }

    /**
     * 更新.
     */
    public function update(int $id, array $data): bool
    {
        /* @var ExamClassify $curModel */
        $curModel = $this->read($id);
        // 如果更新年级或者科目,把下级所有分类的年级科目都更新
        if ((isset($data['grade']) && (int)$data['grade'] !== $curModel->grade) || (isset($data['subject']) && (int)$data['subject'] !== $curModel->subject)) {
            $this->mapper->updateChildren($id, ['grade' => $data['grade'] ?? null, 'subject' => $data['subject'] ?? null]);
        }
        return $this->mapper->update($id, $this->handleData($data));
    }

    /**
     * 真实删除数据，跳过存在子节点的数据.
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
                    array_push($ctuIds, $id);
                }
            }
        }
        return count($ctuIds) ? $this->mapper->getTreeName($ctuIds) : null;
    }

    /**
     * 检查子节点是否存在.
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

    /**
     * 处理数据.
     */
    protected function handleData(array $data): array
    {
        if (isset($data['id'], $data['parent_id']) && (int)$data['id'] === (int)$data['parent_id']) {
            throw new NormalStatusException('上级不能等于本级', 500);
        }

        $pid = $data['parent_id'] ?? 0;

        if ($pid === 0) {
            $data['level'] = $data['parent_id'] = '0';
        } else {
            $data['level'] = $this->read($data['parent_id'])->level . ',' . $data['parent_id'];
        }
        return $data;
    }
}
