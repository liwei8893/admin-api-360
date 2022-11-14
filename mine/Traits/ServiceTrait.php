<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Mine\Traits;

use Closure;
use Hyperf\Database\Model\Collection;
use Hyperf\Utils\Arr;
use Hyperf\Utils\HigherOrderTapProxy;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;
use Mine\MineCollection;
use Mine\MineModel;
use Mine\MineResponse;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

trait ServiceTrait
{
    /**
     * @var AbstractMapper
     */
    public $mapper;

    /**
     * 获取列表数据.
     */
    public function getList(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = false;
        return $this->mapper->getList($params, $isScope);
    }

    /**
     * 从回收站过去列表数据.
     */
    public function getListByRecycle(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = true;
        return $this->mapper->getList($params, $isScope);
    }

    /**
     * 获取列表数据（带分页）.
     */
    public function getPageList(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        return $this->mapper->getPageList($params, $isScope);
    }

    /**
     * 从回收站获取列表数据（带分页）.
     */
    public function getPageListByRecycle(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = true;
        return $this->mapper->getPageList($params, $isScope);
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
        return $this->mapper->getTreeList($params, $isScope);
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
        return $this->mapper->getTreeList($params, $isScope);
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        return $this->mapper->save($data);
    }

    /**
     * 批量新增.
     */
    #[Transaction]
    public function batchSave(array $collects): bool
    {
        foreach ($collects as $collect) {
            $this->mapper->save($collect);
        }
        return true;
    }

    /**
     * 读取一条数据.
     */
    public function read(int $id): ?MineModel
    {
        return $this->mapper->read($id);
    }

    /**
     * Description:获取单个值
     * User:mike.
     * @return null|HigherOrderTapProxy|mixed|void
     */
    public function value(array $condition, string $columns = 'id')
    {
        return $this->mapper->value($condition, $columns);
    }

    /**
     * Description:获取单列值
     * User:mike.
     * @param array $condition
     * @param string $columns
     * @return array
     */
    public function pluck(array $condition, string $columns = 'id'): array
    {
        return $this->mapper->pluck($condition, $columns);
    }

    /**
     * 从回收站读取一条数据.
     * @noinspection PhpUnused
     */
    public function readByRecycle(int $id): MineModel
    {
        return $this->mapper->readByRecycle($id);
    }

    /**
     * 单个或批量软删除数据.
     */
    public function delete(array $ids): bool
    {
        return ! empty($ids) && $this->mapper->delete($ids);
    }

    /**
     * 更新一条数据.
     */
    public function update(int $id, array $data): bool
    {
        return $this->mapper->update($id, $data);
    }

    /**
     * 按条件更新数据.
     */
    public function updateByCondition(array $condition, array $data): bool
    {
        return $this->mapper->updateByCondition($condition, $data);
    }

    /**
     * 单个或批量真实删除数据.
     */
    public function realDelete(array $ids): bool
    {
        return ! empty($ids) && $this->mapper->realDelete($ids);
    }

    /**
     * 单个或批量从回收站恢复数据.
     */
    public function recovery(array $ids): bool
    {
        return ! empty($ids) && $this->mapper->recovery($ids);
    }

    /**
     * 单个或批量禁用数据.
     */
    public function disable(array $ids, string $field = 'status'): bool
    {
        return ! empty($ids) && $this->mapper->disable($ids, $field);
    }

    /**
     * 单个或批量启用数据.
     */
    public function enable(array $ids, string $field = 'status'): bool
    {
        return ! empty($ids) && $this->mapper->enable($ids, $field);
    }

    /**
     * 修改数据状态
     */
    public function changeStatus(int $id, string $value, string $filed = 'status'): bool
    {
        return (int) $value === MineModel::ENABLE ? $this->mapper->enable([$id], $filed) : $this->mapper->disable([$id], $filed);
    }

    /**
     * 数字更新操作.
     */
    public function numberOperation(int $id, string $field, int $value): bool
    {
        return $this->mapper->numberOperation($id, $field, $value);
    }

    /**
     * 导出数据.
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function export(array $params, ?string $dto, string $filename = null): ResponseInterface
    {
        if (empty($dto)) {
            return container()->get(MineResponse::class)->error('导出未指定DTO');
        }

        if (empty($filename)) {
            $filename = $this->mapper->getModel()->getTable();
        }
        $data = $this->mapper->getList($params);
        $this->handleExportData($data);
        return (new MineCollection())->export($dto, $filename, $data);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *                                    author:ZQ
     *                                    time:2022-08-30 17:45
     */
    public function bigExport(array $params, ?string $dto, string $filename = null): ResponseInterface
    {
        if (empty($dto)) {
            return container()->get(MineResponse::class)->error('导出未指定DTO');
        }
        if (empty($filename)) {
            $filename = $this->mapper->getModel()->getTable();
        }
        $closure = function ($fileObject, $pro) use ($params) {
            $rowIndex = 1;
            $this->mapper->getListChunk($params, function ($items) use (&$rowIndex, $fileObject, $pro) {
                foreach ($items as $item) {
                    $item = $item->toArray();
                    $this->handleExportData($item);
                    $columnIndex = 0;
                    foreach ($pro as $property) {
                        if (! empty($property['customField'])) {
                            $value = Arr::get($item, $property['customField']);
                        } else {
                            $value = Arr::get($item, $property['name']);
                        }
                        $fileObject->insertText($rowIndex, $columnIndex, $value);
                        ++$columnIndex;
                    }
                    ++$rowIndex;
                }
            });
        };
        return (new MineCollection())->bigExport($dto, $filename, $closure);
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
        return $this->mapper->import($dto, $closure);
    }

    /**
     * 数组数据转分页数据显示.
     */
    public function getArrayToPageList(?array $params = [], string $pageName = 'page'): array
    {
        $collect = $this->handleArraySearch(collect($this->getArrayData($params)), $params);

        $pageSize = MineModel::PAGE_SIZE;
        $page = 1;

        if ($params[$pageName] ?? false) {
            $page = (int) $params[$pageName];
        }

        if ($params['pageSize'] ?? false) {
            $pageSize = (int) $params['pageSize'];
        }

        $data = $collect->forPage($page, $pageSize)->toArray();

        return [
            'items' => $this->getCurrentArrayPageBefore($data, $params),
            'pageInfo' => [
                'total' => $collect->count(),
                'currentPage' => $page,
                'totalPage' => ceil($collect->count() / $pageSize),
            ],
        ];
    }

    /**
     * 需要处理导出数据时,重写函数.
     */
    protected function handleExportData(array &$data): void
    {
    }

    /**
     * 数组数据搜索器.
     * @return Collection
     */
    protected function handleArraySearch(\Hyperf\Utils\Collection $collect, array $params): \Hyperf\Utils\Collection
    {
        return $collect;
    }

    /**
     * 数组当前页数据返回之前处理器，默认对key重置.
     */
    protected function getCurrentArrayPageBefore(array &$data, array $params = []): array
    {
        sort($data);
        return $data;
    }

    /**
     * 设置需要分页的数组数据.
     */
    protected function getArrayData(array $params = []): array
    {
        return [];
    }
}
