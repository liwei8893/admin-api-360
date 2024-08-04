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

use App\Play\Model\PlayIdiom;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 成语接龙Mapper类
 */
class PlayIdiomMapper extends AbstractMapper
{
    /**
     * @var PlayIdiom
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = PlayIdiom::class;
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

        return $query;
    }
}
