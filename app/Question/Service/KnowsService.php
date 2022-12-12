<?php

declare(strict_types=1);

namespace App\Question\Service;

use App\Question\Mapper\KnowsMapper;
use Mine\Abstracts\AbstractService;

/**
 * 题库管理服务类.
 */
class KnowsService extends AbstractService
{
    /**
     * @var KnowsMapper
     */
    public $mapper;

    public function __construct(KnowsMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getDictData(): array
    {
        $params['select'] = 'id,name as title,id as key';
        $params['status'] = 1;
        $params['orderBy'] = ['sort', 'id'];
        $params['orderType'] = ['desc', 'desc'];
        return $this->getList($params, false);
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        return $this->mapper->save($this->handleData($data));
    }

    /**
     * 更新一条数据.
     */
    public function update(int $id, array $data): bool
    {
        return $this->mapper->update($id, $this->handleData($data));
    }

    /**
     * 获取列表数据（带分页）.
     */
    public function getPageList(?array $params = null, bool $isScope = true): array
    {
        $data = parent::getPageList($params, false);
        foreach ($data['items'] as &$item) {
            $item['shop_id'] = explode(',', $item['shop_id']);
        }
        return $data;
    }

    protected function handleData(array $data): array
    {
        if (isset($data['shop_id']) && is_array($data['shop_id'])) {
            $data['shop_id'] = implode(',', $data['shop_id']);
        }
        return $data;
    }
}
