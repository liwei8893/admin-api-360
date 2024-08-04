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

namespace App\Play\Mapper;

use App\Play\Model\PlayWord;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 单词游戏Mapper类
 */
class PlayWordMapper extends AbstractMapper
{
    /**
     * @var PlayWord
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = PlayWord::class;
    }

    public function getMaxId(): int|null
    {
        return $this->model::max('id');
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        // 关卡等级
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        // 单词
        if (isset($params['word']) && $params['word'] !== '') {
            $query->where('word', 'like', '%' . $params['word'] . '%');
        }

        // 英式英标
        if (isset($params['uk']) && $params['uk'] !== '') {
            $query->where('uk', 'like', '%' . $params['uk'] . '%');
        }

        // 英式发音
        if (isset($params['uk_speech']) && $params['uk_speech'] !== '') {
            $query->where('uk_speech', 'like', '%' . $params['uk_speech'] . '%');
        }

        // 美式英标
        if (isset($params['us']) && $params['us'] !== '') {
            $query->where('us', 'like', '%' . $params['us'] . '%');
        }

        // 美式发音
        if (isset($params['us_speech']) && $params['us_speech'] !== '') {
            $query->where('us_speech', 'like', '%' . $params['us_speech'] . '%');
        }

        return $query;
    }
}
