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

namespace App\Question\Mapper;

use App\Question\Model\QuestionHistory;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 错题表Mapper类
 */
class QuestionHistoryMapper extends AbstractMapper
{
    /**
     * @var QuestionHistory
     */
    public $model;

    public function assignModel()
    {
        $this->model = QuestionHistory::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }
        // 用户输入的答案
        if (isset($params['user_answer']) && $params['user_answer'] !== '') {
            $query->where('user_answer', '=', $params['user_answer']);
        }

        // 0错误；1正确；
        if (isset($params['is_right']) && $params['is_right'] !== '') {
            $query->where('is_right', '=', $params['is_right']);
        }

        //
        if (isset($params['is_mark']) && $params['is_mark'] !== '') {
            $query->where('is_mark', '=', $params['is_mark']);
        }

        // 收藏错题本1收藏,0不收藏
        if (isset($params['is_collect']) && $params['is_collect'] !== '') {
            $query->where('is_collect', '=', $params['is_collect']);
        }

        if (isset($params['created_at'][0], $params['created_at'][1])) {
            $query->whereBetween(
                'created_at',
                [strtotime($params['created_at'][0] . ' 00:00:00'), strtotime($params['created_at'][1] . ' 23:59:59')]
            );
        }

        if (!empty($params['withQuestion'])) {
            $query->with(['question' => function ($query) {
                $query->with(['questionSubject:value,label', 'questionType:value,label', 'knows:id,name,season']);
            }]);
        }
        return $query;
    }
}