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

use App\Play\Model\PlayUserRecord;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractMapper;

/**
 * 用户游戏记录Mapper类
 */
class PlayUserRecordMapper extends AbstractMapper
{
    /**
     * @var PlayUserRecord
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = PlayUserRecord::class;
    }

    public function readByUserId(int $userId): Model|PlayUserRecord|Builder|null
    {
        return $this->model::query()->where('user_id', '=', $userId)->first();
    }

    public function saveOrUpdate(array $params): Model|PlayUserRecord|Builder
    {
        return $this->model::query()->updateOrCreate(['user_id' => $params['user_id']], $params);
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        // 成语接龙关卡等级
        if (isset($params['idiom_level']) && $params['idiom_level'] !== '') {
            $query->where('idiom_level', '=', $params['idiom_level']);
        }

        // 数独最高分数
        if (isset($params['sudoku_score']) && $params['sudoku_score'] !== '') {
            $query->where('sudoku_score', '=', $params['sudoku_score']);
        }

        if (!empty($params['withUser'])) {
            $query->with('user:id,user_name,mobile,old_platform');
        }

        return $query;
    }
}
