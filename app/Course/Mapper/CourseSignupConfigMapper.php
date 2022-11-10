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

use App\Course\Model\CourseSignupConfig;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;

/**
 * 课程报名配置表Mapper类
 */
class CourseSignupConfigMapper extends AbstractMapper
{
    /**
     * @var CourseSignupConfig
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CourseSignupConfig::class;
    }

    #[Transaction]
    public function save(array $data): int
    {
        $courseIds = $data['course_ids'] ?? [];
        $gradeIds = $data['grade_ids'] ?? [];
        $this->filterExecuteAttributes($data, true);
        $model = $this->model::create($data);
        $model->courseSignup()->sync($courseIds);
        $model->gradeSignup()->sync($gradeIds);
        return $model->id;
    }

    #[Transaction]
    public function update(int $id, array $data): bool
    {
        $courseIds = $data['course_ids'] ?? [];
        $gradeIds = $data['grade_ids'] ?? [];
        $this->filterExecuteAttributes($data, true);
        $model = $this->model::find($id);
        $updateState = $model->update($data) > 0;
        $model->courseSignup()->sync($courseIds);
        $model->gradeSignup()->sync($gradeIds);
        return $updateState;
    }


    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        // 主键
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        // 备注
        if (isset($params['remark']) && $params['remark'] !== '') {
            $query->where('remark', '=', $params['remark']);
        }

        // 创建者
        if (isset($params['created_by']) && $params['created_by'] !== '') {
            $query->where('created_by', '=', $params['created_by']);
        }

        // 更新者
        if (isset($params['updated_by']) && $params['updated_by'] !== '') {
            $query->where('updated_by', '=', $params['updated_by']);
        }

        //
        if (isset($params['created_at']) && $params['created_at'] !== '') {
            $query->where('created_at', '=', $params['created_at']);
        }

        //
        if (isset($params['updated_at']) && $params['updated_at'] !== '') {
            $query->where('updated_at', '=', $params['updated_at']);
        }

        //
        if (isset($params['deleted_at']) && $params['deleted_at'] !== '') {
            $query->where('deleted_at', '=', $params['deleted_at']);
        }
        if (!empty($params['withCourse'])) {
            $query->with(['courseSignup:id,title']);
        }
        if (!empty($params['withGrade'])) {
            $query->with(['gradeSignup']);
        }

        return $query;
    }
}