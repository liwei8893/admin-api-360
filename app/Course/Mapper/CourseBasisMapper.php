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

namespace App\Course\Mapper;

use App\Course\Model\CourseBasis;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 课时详情表Mapper类
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
     * 批量更新
     * @param $ids
     * @param $data
     * @return int
     * author:ZQ
     * time:2022-08-21 17:54
     */
    public function batchUpdate($ids, $data): int
    {
        return $this->model::query()->whereIn('id', $ids)->update($data);
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 课程类型：1直播, 4公开课, 5录播课, 7讲座, 8音频课, 9系统课
        if (isset($params['course_type']) && $params['course_type'] !== '') {
            $query->where('course_type', '=', $params['course_type']);
        }

        // 状态
        if (isset($params['states']) && $params['states'] !== '') {
            $query->where('states', '=', $params['states']);
        }

        // 是否删除
        if (!isset($params['is_del'])) {
            $query->where('is_del', '=', 0);
        }
        if (!empty($params['is_del'])) {
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
        // 年级
        if (isset($params['grade_id']) && $params['grade_id'] !== '') {
            $query->where('grade_id', '=', $params['grade_id']);
        }
        if (isset($params['grade'])&&is_array($params['grade'])){
            $query->whereHas('basisGrade',function (Builder $query) use ($params){
                $query->whereIn('grade_id',$params['grade']);
            });
        }

        // 科目
        if (isset($params['subject_id']) && $params['subject_id'] !== '') {
            $query->where('subject_id', '=', $params['subject_id']);
        }

        if (!empty($params['course_title'])) {
            $query->where('course_title', $params['course_title']);
        }

        if (!empty($params['withBasisType'])) {
            $query->with(['basisType:id,name']);
        }

        if (!empty($params['withBasisGrade'])) {
            $query->with(['basisGrade']);
        }

        if (!empty($params['withCountChapter'])) {
            $query->withCount(['chapter' => function (Builder $query) {
                $query->where('parent_id', '!=', 0);
            }]);
        }

        return $query;
    }
}