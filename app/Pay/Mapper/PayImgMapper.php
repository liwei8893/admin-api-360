<?php

declare(strict_types=1);

namespace App\Pay\Mapper;

use App\Pay\Model\PayImg;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 图片配置Mapper类.
 */
class PayImgMapper extends AbstractMapper
{
    /**
     * @var PayImg
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = PayImg::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        // 备注
        if (isset($params['remark']) && $params['remark'] !== '') {
            $query->where('remark', 'like', '%' . $params['remark'] . '%');
        }

        // 图片地址
        if (isset($params['img']) && $params['img'] !== '') {
            $query->where('img', 'like', '%' . $params['img'] . '%');
        }

        return $query;
    }
}
