<?php

declare(strict_types=1);

namespace App\Operation\Mapper;

use App\Operation\Model\Information;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 资讯列表Mapper类.
 */
class InformationMapper extends AbstractMapper
{
    /**
     * @var Information
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Information::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        // 资讯分类id
        if (isset($params['classify_id']) && $params['classify_id'] !== '') {
            $query->where('classify_id', '=', $params['classify_id']);
        }

        // 资讯标题
        if (isset($params['information_title']) && $params['information_title'] !== '') {
            $query->where('information_title', '=', $params['information_title']);
        }

        // 资讯图片
        if (isset($params['picture']) && $params['picture'] !== '') {
            $query->where('picture', '=', $params['picture']);
        }

        // 资讯来源
        if (isset($params['source']) && $params['source'] !== '') {
            $query->where('source', '=', $params['source']);
        }

        // 摘要
        if (isset($params['abstract']) && $params['abstract'] !== '') {
            $query->where('abstract', '=', $params['abstract']);
        }

        // 资讯内容
        if (isset($params['content']) && $params['content'] !== '') {
            $query->where('content', '=', $params['content']);
        }

        // 状态：1已发布 2未发布
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', '=', $params['status']);
        }

        // 点击率
        if (isset($params['click_rate']) && $params['click_rate'] !== '') {
            $query->where('click_rate', '=', $params['click_rate']);
        }
        if (! empty($params['withInformationType'])) {
            $query->with('informationType:id,name');
        }
        return $query;
    }
}
