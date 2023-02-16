<?php

declare(strict_types=1);

namespace Mine\Traits;

use Closure;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\ModelCache\Manager;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\HigherOrderTapProxy;
use Mine\Annotation\Transaction;
use Mine\MineCollection;
use Mine\MineModel;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait MapperTrait
{
    /**
     * @var MineModel|string
     */
    public $model;

    /**
     * 获取列表数据.
     */
    public function getList(?array $params, bool $isScope = true): array
    {
        return $this->listQuerySetting($params, $isScope)->get()->toArray();
    }

    /**
     * 获取列表数据（带分页）.
     */
    public function getPageList(?array $params, bool $isScope = true, string $pageName = 'page'): array
    {
        $page = $params[$pageName] ?? 1;
        $pageSize = $params['pageSize'] ?? $this->model::PAGE_SIZE;
        $paginate = $this->listQuerySetting($params, $isScope)->paginate(
            (int)$pageSize,
            ['*'],
            $pageName,
            (int)$page
        );
        return $this->setPaginate($paginate);
    }

    /**
     * 设置数据库分页.
     */
    public function setPaginate(LengthAwarePaginatorInterface $paginate): array
    {
        return [
            'items' => $paginate->items(),
            'pageInfo' => [
                'total' => $paginate->total(),
                'currentPage' => $paginate->currentPage(),
                'totalPage' => $paginate->lastPage(),
            ],
        ];
    }

    /**
     * 获取树列表.
     */
    public function getTreeList(
        ?array $params = null,
        bool   $isScope = true,
        string $id = 'id',
        string $parentField = 'parent_id',
        string $children = 'children'
    ): array
    {
        $params['_mineadmin_tree'] = true;
        $params['_mineadmin_tree_pid'] = $parentField;
        $data = $this->listQuerySetting($params, $isScope)->get();
        return $data->toTree([], $data[0]->{$parentField} ?? 0, $id, $parentField, $children);
    }

    /**
     * 返回模型查询构造器.
     */
    public function listQuerySetting(?array $params, bool $isScope): Builder
    {
        $query = (($params['recycle'] ?? false) === true) ? $this->model::onlyTrashed() : $this->model::query();

        if ($params['select'] ?? false) {
            $query->select($this->filterQueryAttributes($params['select']));
        }

        $query = $this->handleOrder($query, $params);

        $isScope && $query->userDataScope();
        $isScope && $this->model === 'App\Users\Model\User' && $query->platformDataScope();

        return $this->handleSearch($query, $params);
    }

    /**
     * 排序处理器.
     */
    public function handleOrder(Builder $query, ?array &$params = null): Builder
    {
        // 对树型数据强行加个排序
        if (isset($params['_mineadmin_tree'])) {
            $query->orderBy($params['_mineadmin_tree_pid']);
        }

        if ($params['orderBy'] ?? false) {
            if (is_array($params['orderBy'])) {
                foreach ($params['orderBy'] as $key => $order) {
                    $query->orderBy($order, $params['orderType'][$key] ?? 'asc');
                }
            } else {
                $query->orderBy($params['orderBy'], $params['orderType'] ?? 'asc');
            }
        }

        return $query;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query;
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        $model = $this->model::create($data);
        return $model->{$model->getKeyName()};
    }

    /**
     * 新增数据.
     */
    public function saveModel(array $data): MineModel|Model
    {
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        return $this->model::create($data);
    }

    /**
     * 读取一条数据.
     */
    public function read(int $id): ?MineModel
    {
        return ($model = $this->model::find($id)) ? $model : null;
    }

    /**
     * 按条件读取一行数据.
     * @return mixed
     */
    public function first(array $condition, array $column = ['*']): ?MineModel
    {
        return ($model = $this->model::where($condition)->first($column)) ? $model : null;
    }

    /**
     * 获取单个值
     * @return null|HigherOrderTapProxy|mixed|void
     */
    public function value(array $condition, string $columns = 'id')
    {
        return ($model = $this->model::where($condition)->value($columns)) ? $model : null;
    }

    /**
     * 获取单列值
     */
    public function pluck(array $condition, string $columns = 'id'): array
    {
        return $this->model::where($condition)->pluck($columns)->toArray();
    }

    /**
     * 从回收站读取一条数据.
     * @noinspection PhpUnused
     */
    public function readByRecycle(int $id): ?MineModel
    {
        return ($model = $this->model::withTrashed()->find($id)) ? $model : null;
    }

    /**
     * 单个或批量软删除数据.
     */
    public function delete(array $ids): bool
    {
        $this->model::destroy($ids);

        $manager = ApplicationContext::getContainer()->get(Manager::class);
        $manager->destroy($ids, $this->model);

        return true;
    }

    /**
     * 按条件更新数据.
     */
    public function updateByCondition(array $condition, array $data): bool
    {
        $this->filterExecuteAttributes($data, true);
        return $this->model::query()->where($condition)->update($data) > 0;
    }

    /**
     * 更新一条数据.
     */
    public function update(int $id, array $data): bool
    {
        $this->filterExecuteAttributes($data, true);
        return $this->model::find($id)->update($data) > 0;
    }

    /**
     * 单个或批量真实删除数据.
     */
    public function realDelete(array $ids): bool
    {
        foreach ($ids as $id) {
            $model = $this->model::withTrashed()->find($id);
            $model && $model->forceDelete();
        }
        return true;
    }

    /**
     * 单个或批量从回收站恢复数据.
     */
    public function recovery(array $ids): bool
    {
        $this->model::withTrashed()->whereIn((new $this->model())->getKeyName(), $ids)->restore();
        return true;
    }

    /**
     * 单个或批量禁用数据.
     */
    public function disable(array $ids, string $field = 'status'): bool
    {
        $this->model::query()->whereIn((new $this->model())->getKeyName(), $ids)->update([$field => $this->model::DISABLE]);
        return true;
    }

    /**
     * 单个或批量启用数据.
     */
    public function enable(array $ids, string $field = 'status'): bool
    {
        $this->model::query()->whereIn((new $this->model())->getKeyName(), $ids)->update([$field => $this->model::ENABLE]);
        return true;
    }

    public function getModel(): MineModel
    {
        return new $this->model();
    }

    /**
     * 数据导入.
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Transaction]
    public function import(string $dto, ?Closure $closure = null): bool
    {
        return (new MineCollection())->import($dto, $this->getModel(), $closure);
    }

    /**
     * 闭包通用查询设置.
     * @param null|Closure $closure 传入的闭包查询
     */
    public function settingClosure(?Closure $closure = null): Builder
    {
        return $this->model::where(function ($query) use ($closure) {
            if ($closure instanceof Closure) {
                $closure($query);
            }
        });
    }

    /**
     * 闭包通用方式查询一条数据.
     * @param array|string[] $column
     * @return null|Builder|Model
     */
    public function one(?Closure $closure = null, array $column = ['*'])
    {
        return $this->settingClosure($closure)->select($column)->first();
    }

    /**
     * 闭包通用方式查询数据集合.
     * @param array|string[] $column
     */
    public function get(?Closure $closure = null, array $column = ['*']): array
    {
        return $this->settingClosure($closure)->get($column)->toArray();
    }

    /**
     * 闭包通用方式统计
     */
    public function count(?Closure $closure = null, string $column = '*'): int
    {
        return $this->settingClosure($closure)->count($column);
    }

    /**
     * 闭包通用方式查询最大值
     * @return mixed|string|void
     */
    public function max(?Closure $closure = null, string $column = '*')
    {
        return $this->settingClosure($closure)->max($column);
    }

    /**
     * 闭包通用方式查询最小值
     * @return mixed|string|void
     */
    public function min(?Closure $closure = null, string $column = '*')
    {
        return $this->settingClosure($closure)->min($column);
    }

    /**
     * 数字更新操作.
     */
    public function numberOperation(int $id, string $field, int $value): bool
    {
        return $this->update($id, [$field => $value]);
    }

    /**
     * 过滤新增或写入不存在的字段.
     */
    public function comFilterExecuteAttributes(string $modelClass, array &$data, bool $removePk = true): void
    {
        $model = new $modelClass();
        $attrs = $model->getFillable();
        foreach ($data as $name => $val) {
            if (!in_array($name, $attrs, true)) {
                unset($data[$name]);
            }
        }
        if ($removePk && isset($data[$model->getKeyName()])) {
            unset($data[$model->getKeyName()]);
        }
        $model = null;
    }

    /**
     * chunk.
     * @param null $column
     * @param null $alias
     */
    public function getListChunk(?array $params, Closure $callback, bool $isScope = true, $column = null, $alias = null): bool
    {
        // chunkById 不能跟排序一起用
        unset($params['orderBy'], $params['orderType']);
        return $this->listQuerySetting($params, $isScope)->chunkById(1000, $callback, $column, $alias);
    }

    /**
     * 过滤查询字段不存在的属性.
     */
    protected function filterQueryAttributes(array $fields, bool $removePk = false): array
    {
        $model = new $this->model();
        $attrs = $model->getFillable();
        foreach ($fields as $key => $field) {
            if (!in_array(trim($field), $attrs) && mb_strpos(str_replace('AS', 'as', $field), 'as') === false) {
                unset($fields[$key]);
            } else {
                $fields[$key] = trim($field);
            }
        }
        if ($removePk && in_array($model->getKeyName(), $fields)) {
            unset($fields[array_search($model->getKeyName(), $fields)]);
        }
        $model = null;
        return (count($fields) < 1) ? ['*'] : $fields;
    }

    /**
     * 过滤新增或写入不存在的字段.
     */
    protected function filterExecuteAttributes(array &$data, bool $removePk = false): void
    {
        $model = new $this->model();
        $attrs = $model->getFillable();
        foreach ($data as $name => $val) {
            if (!in_array($name, $attrs)) {
                unset($data[$name]);
            }
        }
        if ($removePk && isset($data[$model->getKeyName()])) {
            unset($data[$model->getKeyName()]);
        }
        $model = null;
    }
}
