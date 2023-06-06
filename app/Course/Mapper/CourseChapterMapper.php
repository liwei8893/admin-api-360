<?php

declare(strict_types=1);

namespace App\Course\Mapper;

use App\Course\Model\CourseChapter;
use App\Course\Model\CoursePeriod;
use Exception;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Relations\HasOne;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;

/**
 * 课程大纲Mapper类.
 */
class CourseChapterMapper extends AbstractMapper
{
    /**
     * @var CourseChapter
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CourseChapter::class;
    }

    /**
     * 获取前端选择树.
     */
    public function getSelectTree(): array
    {
        return $this->model::query()
            ->select(['id', 'parent_id', 'id AS value', 'title AS label'])
            ->get()->toTree();
    }

    /**
     * 查询树名称.
     */
    public function getTreeName(array $ids = null): array
    {
        return $this->model::withTrashed()->whereIn('id', $ids)->pluck('title')->toArray();
    }

    #[Transaction]
    public function updateChapter(int $id, array $data): bool
    {
        $chapterModel = $this->model::query()->find($id);
        $periodModel = $chapterModel->coursePeriod;
        $periodModel->tags()->sync($data['tag']);
        $periodModel->questionPeriod()->sync($data['question_period']);

        $this->comFilterExecuteAttributes(CoursePeriod::class, $data['course_period']);
        $chapterModel->coursePeriod()->update($data['course_period']);

        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        return $chapterModel->update($data);
    }

    #[Transaction]
    public function saveChapter(array $data): int
    {
        $chapterModel = $this->model::create($data);
        $periodModel = $chapterModel->coursePeriod()->create($data['course_period']);
        $periodModel->tags()->sync($data['tag']);
        $periodModel->questionPeriod()->sync($data['question_period']);
        return $chapterModel->id;
    }

    /**
     * @throws Exception
     */
    #[Transaction]
    public function delete(array $ids): bool
    {
        foreach ($ids as $id) {
            $chapterModel = $this->model::query()->find($id);
            if ($this->checkChildrenExists($id)) {
                continue;
            }
            if ($chapterModel['parent_id'] !== 0) {
                $chapterModel->coursePeriod->tags()->delete();
                $chapterModel->coursePeriod()->delete();
            }
            $chapterModel->delete();
        }
        return true;
    }

    public function checkChildrenExists(int $id): bool
    {
        return $this->model::query()->where('parent_id', $id)->exists();
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        // 课程基本信息ID
        if (isset($params['course_basis_id']) && $params['course_basis_id'] !== '') {
            $query->where('course_basis_id', '=', $params['course_basis_id']);
        }

        // 章的名称
        if (isset($params['title']) && $params['title'] !== '') {
            $query->where('title', 'like', "%{$params['title']}%");
        }

        if (! empty($params['withCoursePeriod'])) {
            $query->with(['coursePeriod' => function (HasOne $query) {
                $query->with(['teacher', 'tags', 'questionPeriod'])
                    ->select(array_merge(CoursePeriod::COMMON_FIELDS, ['qiniu_url']));
            }]);
        }
        if (! empty($params['withAppCoursePeriod'])) {
            $query->with(['coursePeriod' => function (HasOne $query) {
                $query->with(['teacher', 'tags:id,name'])
                    ->select(CoursePeriod::COMMON_FIELDS)
                    // withCount要在select后面
                    ->withCount(['questionPeriod as question_test_count']);
            }]);
        }

        if (! empty($params['withCourseBasis'])) {
            $query->with('courseBasis');
        }

        return $query;
    }
}
