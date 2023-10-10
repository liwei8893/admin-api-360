<?php

declare(strict_types=1);

namespace App\Course\Mapper;

use App\Course\Model\CourseBasis;
use Carbon\Carbon;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;

/**
 * 课时详情表Mapper类.
 */
class CourseBasisMapper extends AbstractMapper
{
    /**
     * @var CourseBasis
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CourseBasis::class;
    }

    /**
     * 批量更新.
     */
    public function batchUpdate(array $ids, array $data): int
    {
        return $this->model::query()->whereIn('id', $ids)->update($data);
    }

    #[Transaction]
    public function update(int $id, array $data): bool
    {
        // 拷贝原始数据
        $rawData = $data;
        // 过滤数据
        $this->filterExecuteAttributes($data, true);
        $model = $this->model::find($id);
        if (! $model) {
            return false;
        }
        $state = $model->update($data);
        if (isset($rawData['grade'])) {
            $model->basisGrade()->sync($rawData['grade']);
        }
        return $state;
    }

    #[Transaction]
    public function save(array $data): int
    {
        $grade = $data['grade'] ?? [];
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        $model = $this->model::create($data);
        $model->basisGrade()->sync($grade);
        return $model->id;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 课程类型：1直播, 4公开课, 5录播课, 7讲座, 8音频课, 9系统课
        if (isset($params['course_type']) && $params['course_type'] !== '') {
            $query->where('course_type', '=', $params['course_type']);
        }

        if (isset($params['id']) && ! is_array($params['id'])) {
            $query->where('id', $params['id']);
        }

        if (isset($params['id']) && is_array($params['id'])) {
            $query->whereIn('id', $params['id']);
        }

        if (isset($params['course_second_title']) && ! is_array($params['course_second_title'])) {
            $query->where('course_second_title', $params['course_second_title']);
        }

        if (isset($params['course_second_title']) && is_array($params['course_second_title'])) {
            $query->whereIn('course_second_title', $params['course_second_title']);
        }

        if (isset($params['is_give'])) {
            $query->where('is_give', $params['is_give']);
        }

        if (isset($params['vip_type'])) {
            $query->where('vip_type', $params['vip_type']);
        }

        // 状态
        if (isset($params['states']) && $params['states'] !== '') {
            $query->where('states', '=', $params['states']);
        }

        // 是否删除
        if (! isset($params['is_del'])) {
            $query->where('is_del', '=', 0);
        }
        if (! empty($params['is_del'])) {
            $query->where('is_del', '=', $params['is_del']);
        }

        // title
        if (isset($params['title'])) {
            $query->where('title', 'like', "%{$params['title']}%");
        }

        // 是否开始报名
        if (isset($params['is_signup'])) {
            $query->where('is_signup', '=', $params['is_signup']);
        }
        // 季节
        if (isset($params['season'])) {
            $query->where('season', $params['season']);
        }
        // 年级
        if (isset($params['grade']) && ! is_array($params['grade'])) {
            $query->whereHas('basisGrade', function (Builder $query) use ($params) {
                $query->where('grade_id', $params['grade']);
            });
        }
        if (isset($params['grade']) && is_array($params['grade'])) {
            $query->whereHas('basisGrade', function (Builder $query) use ($params) {
                $query->whereIn('grade_id', $params['grade']);
            });
        }

        // 科目
        if (isset($params['subject_id']) && $params['subject_id'] !== '') {
            $query->where('subject_id', '=', $params['subject_id']);
        }
        if (isset($params['subject']) && $params['subject'] !== '') {
            $query->where('subject_id', '=', $params['subject']);
        }

        if (! empty($params['course_title'])) {
            $query->where('course_title', $params['course_title']);
        }

        if (! empty($params['withBasisType'])) {
            $query->with(['basisType:id,name']);
        }

        if (! empty($params['withBasisGrade'])) {
            $query->with(['basisGrade']);
        }

        if (! empty($params['withCountChapter'])) {
            $query->withCount(['chapter' => function (Builder $query) {
                $query->where('parent_id', '!=', 0);
            }]);
        }

        if (! empty($params['excludeShop'])) {
            $query->has('scoreShop', '<');
        }
        // 章节一个月内是否有更新
        if (! empty($params['periodUpdateCount'])) {
            $query->withCount(['period as period_update_count' => function (Builder $query) {
                $query->where('updated_at', '>=', Carbon::now()->subDays(30)->timestamp);
            }]);
        }
        return $query;
    }
}
