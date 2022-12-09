<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\CourseChapterMapper;
use Mine\Abstracts\AbstractService;

/**
 * 课程大纲服务类.
 */
class CourseChapterService extends AbstractService
{
    /**
     * @var CourseChapterMapper
     */
    public $mapper;

    public function __construct(CourseChapterMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 获取树列表.
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
        if ($data['parent_id'] === 0) {
            return $this->mapper->save($this->handleData($data));
        }
        $data['course_period'] = array_merge($this->handlePeriodInsetData($data), $data['course_period']);
        return $this->mapper->saveChapter($data);
    }

    public function handlePeriodInsetData(array $data): array
    {
        return [
            'course_basis_id' => $data['course_basis_id'],
            'title' => $data['title'],
            'start_play' => $data['start_play'] ?? 0,
            'end_play' => $data['end_play'] ?? 0,
            'qiniu_url' => $data['qiniu_url'] ?? '',
        ];
    }

    /**
     * 更新.
     */
    public function update(int $id, array $data): bool
    {
        return $this->mapper->update($id, $this->handleData($data));
    }

    /**
     * 真实删除数据，跳过存在子节点的数据.
     * @return array
     */
    public function realDel(array $ids): ?array
    {
        // 存在子节点，跳过的数据
        $ctuIds = [];
        if (count($ids)) {
            foreach ($ids as $id) {
                if (! $this->checkChildrenExists((int) $id)) {
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
     * 处理数据.
     */
    protected function handleData(array $data): array
    {
        if (is_array($data['parent_id']) && ! empty($data['parent_id'])) {
            $data['parent_id'] = array_pop($data['parent_id']);
        }
        if (isset($data['course_period'])) {
            // 同步chapter和period部分数据
            $data['course_period']['title'] = $data['title'];
        }
        return $data;
    }
}
