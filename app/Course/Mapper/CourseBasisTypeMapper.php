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

use App\Course\Model\CourseBasisType;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 课程分类Mapper类
 */
class CourseBasisTypeMapper extends AbstractMapper
{
    /**
     * @var CourseBasisType
     */
    public $model;

    public function assignModel()
    {
        $this->model = CourseBasisType::class;
    }

    /**
     * 获取前端选择树
     * @return array
     */
    public function getSelectTree(): array
    {
        return $this->model::query()
            ->select(['id', 'parent_id', 'id AS value', 'name AS label'])
            ->get()->toTree();
    }


    /**
     * 查询树名称
     * @param array|null $ids
     * @return array
     */
    public function getTreeName(array $ids = null): array
    {
        return $this->model::withTrashed()->whereIn('id', $ids)->pluck('name')->toArray();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function checkChildrenExists(int $id): bool
    {
        return $this->model::withTrashed()->where('parent_id', $id)->exists();
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        //
        if (isset($params['name']) && $params['name'] !== '') {
            $query->where('name', '=', $params['name']);
        }

        //
        if (isset($params['parent_id']) && $params['parent_id'] !== '') {
            $query->where('parent_id', '=', $params['parent_id']);
        }

        //
        if (isset($params['level']) && $params['level'] !== '') {
            $query->where('level', '=', $params['level']);
        }

        //
        if (isset($params['states']) && $params['states'] !== '') {
            $query->where('states', '=', $params['states']);
        }

        //
        if (isset($params['title_id']) && $params['title_id'] !== '') {
            $query->where('title_id', '=', $params['title_id']);
        }

        return $query;
    }
}