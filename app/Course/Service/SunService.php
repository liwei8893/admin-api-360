<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\SunMapper;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;

/**
 * 晒一晒服务类.
 */
class SunService extends AbstractService
{
    /**
     * @var SunMapper
     */
    public $mapper;

    public function __construct(SunMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 晒一晒弹幕接口.
     */
    public function sunContentPageList(array $params): array
    {
        $params['status'] = 1;
        $params['orderBy'] = ['id'];
        $params['orderType'] = ['desc'];
        $params['select'] = 'id,html';
        $data = $this->getPageList($params);
        foreach ($data['items'] as &$item) {
            $item['html'] = $this->StringExtractionText($item['html']);
        }
        return $data;
    }

    /**
     * @param int $num 截取字符的个数
     */
    public function StringExtractionText(string $string, int $num = 50): string
    {
        if (! $string) {
            return '';
        }
        // 把一些预定义的 HTML 实体转换为字符
        // 预定义字符是指:<,>,&等有特殊含义(<,>,用于链接签,&用于转义),不能直接使用
        $html_string = htmlspecialchars_decode($string);
        // 将空格去除
        $content = str_replace('&nbsp;', '', $html_string);
        // 去除字符串中的 HTML 标签
        $contents = strip_tags($content);
        // 利用三元运算判断文字是否超出设置的字数进行截取
        return mb_strlen($contents, 'utf-8') > $num ? mb_substr($contents, 0, $num, 'utf-8') . '...' : mb_substr($contents, 0, $num, 'utf-8');
    }

    public function getAppPageList(array $params): array
    {
        $params = $this->handleData($params);
        return $this->getPageList($params);
    }

    public function vote($params): array
    {
        return $this->mapper->voteToggle($params['id'], user('app')->getId());
    }

    public function delete(array $ids): bool
    {
        foreach ($ids as $id) {
            // 判断是否是自己的内容,用户只能删除自己发布的
            $sunModel = $this->mapper->read($id);
            if (! $sunModel) {
                throw new NormalStatusException('内容不存在!');
            }
            if (user('app')->getId() !== $sunModel['user_id']) {
                throw new NormalStatusException('只能删除自己发布的内容!');
            }
        }
        return parent::delete($ids);
    }

    protected function handleData(array $params): array
    {
        if (! isset($params['status'])) {
            $params['status'] = 1;
        }
        if (! isset($params['withUser'])) {
            $params['withUser'] = true;
        }
        if (! isset($params['withUserVoteCount'])) {
            $params['withUserVoteCount'] = true;
        }
        if (! isset($params['orderBy'])) {
            $params['orderBy'] = ['user_vote_count', 'id'];
        }
        if (! isset($params['orderType'])) {
            $params['orderType'] = ['desc', 'desc'];
        }
        if (! isset($params['withUserNoAudit'])) {
            $params['withUserNoAudit'] = true;
        }
        return $params;
    }

    /**
     * 需要处理导出数据时,重写函数.
     */
    protected function handleExportData(array &$data): void
    {
        $data['html'] = strip_tags($data['html'], '<a><img>');
    }
}
