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

namespace App\Ai\Mapper;

use App\Ai\Model\AiQuesDetailStat;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 题目详情统计Mapper类
 */
class AiQuesDetailStatMapper extends AbstractMapper
{
    /**
     * @var AiQuesDetailStat
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = AiQuesDetailStat::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        
        // 总做题人数
        if (isset($params['total_user_count']) && $params['total_user_count'] !== '') {
            $query->where('total_user_count', '=', $params['total_user_count']);
        }

        // 正确题目数
        if (isset($params['ques_correct_count']) && $params['ques_correct_count'] !== '') {
            $query->where('ques_correct_count', '=', $params['ques_correct_count']);
        }

        // 错误题目数
        if (isset($params['ques_incorrect_count']) && $params['ques_incorrect_count'] !== '') {
            $query->where('ques_incorrect_count', '=', $params['ques_incorrect_count']);
        }

        // 题目正确率
        if (isset($params['ques_correct_rate']) && $params['ques_correct_rate'] !== '') {
            $query->where('ques_correct_rate', '=', $params['ques_correct_rate']);
        }

        return $query;
    }
}