<?php

declare(strict_types=1);

namespace App\Question\Mapper;

use App\Question\Model\DenseVolume;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 黄冈密卷Mapper类.
 */
class DenseVolumeMapper extends AbstractMapper
{
    /**
     * @var DenseVolume
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = DenseVolume::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 视频解析月
        if (isset($params['month']) && $params['month'] !== '') {
            $query->where('month', '=', $params['month']);
        }

        // 视频解析名称
        if (isset($params['name']) && $params['name'] !== '') {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        // 一年级:1,二年级:2,三年级:5,四年级:7,五年级:9,六年级:11,七年级:14,八年级:13,九年级:12,高中:51
        if (isset($params['grade']) && $params['grade'] !== '') {
            $query->where('grade', '=', $params['grade']);
        }

        // 语文:3,数学:4,英语:6,物理:15,化学:8,生物:25,地理:26,政治:23,历史:24,文综53,理综:54
        if (isset($params['subject']) && $params['subject'] !== '') {
            $query->where('subject', '=', $params['subject']);
        }

        // 1月考优题,2期中优题,3期末优题,4中考优题,5高一优题,6高二优题,7高三优题
        if (isset($params['type']) && $params['type'] !== '') {
            $query->where('type', '=', $params['type']);
        }

        // 0试题,1答案
        if (isset($params['answer']) && $params['answer'] !== '') {
            $query->where('answer', '=', $params['answer']);
        }

        // 解析URL
        if (isset($params['url']) && $params['url'] !== '') {
            $query->where('url', '=', $params['url']);
        }

        // 是否最新,最新置顶
        if (isset($params['new_state']) && $params['new_state'] !== '') {
            $query->where('new_state', '=', $params['new_state']);
        }

        // 难度字段 0:全部,1一星,2二星,3三星,4四星,5五星
        if (isset($params['difficulty']) && $params['difficulty'] !== '') {
            $query->where('difficulty', '=', $params['difficulty']);
        }

        // 排序
        if (isset($params['sort']) && $params['sort'] !== '') {
            $query->where('sort', '=', $params['sort']);
        }

        return $query;
    }
}
