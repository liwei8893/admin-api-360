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
        // 新建章
        if ($data['parent_id'] === 0) {
            return $this->mapper->save($data);
        }
        // 新建节
        return $this->mapper->saveChapter($this->handlePeriodData($data));
    }

    /**
     * 更新.
     */
    public function update(int $id, array $data): bool
    {
        // 更新章
        if ($data['parent_id'] === 0) {
            return $this->mapper->update($id, $data);
        }
        // 更新节
        return $this->mapper->updateChapter($id, $this->handlePeriodData($data));
    }

    /**
     * 测一测数据加上type1,练一练加上type2.
     */
    public function handleQuestionPeriodData(array $data): array
    {
        $questionPeriodData = [];
        foreach ($data as $item) {
            $questionPeriodData[$item] = ['type' => 1];
        }
        return $questionPeriodData;
    }

    public function getChapter(int $id): array
    {
        $params['course_basis_id'] = $id;
        $params['withAppCoursePeriod'] = true;
        $params['orderBy'] = ['serial_num', 'id'];
        $params['orderType'] = ['asc', 'asc'];
        return $this->getTreeList($params);
    }

    /**
     * 处理节数据.
     */
    protected function handlePeriodData(array $data): array
    {
        if (isset($data['qurstion_str']) && is_array($data['qurstion_str'])) {
            $data['qurstion_str'] = implode(',', $data['qurstion_str']);
        }
        $data['course_period'] = $data['course_period'] ?? [];
        $initPeriodData = [
            'title' => $data['title'],
            'course_basis_id' => $data['course_basis_id'],
            'start_play' => $data['course_period']['start_play'] ?? 0,
            'end_play' => $data['course_period']['end_play'] ?? 0,
            'qiniu_url' => $data['course_period']['qiniu_url'] ?? '',
            'qurstion_str' => $data['qurstion_str'] ?? '',
        ];
        $data['course_period'] = array_merge($data['course_period'], $initPeriodData);
        // 处理测一测数据
        $data['question_period'] = $this->handleQuestionPeriodData($data['question_period'] ?? []);
        // 处理标签
        $data['tag'] = $data['tag'] ?? [];
        return $data;
    }
}
