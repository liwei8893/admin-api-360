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

use App\Ai\Model\AiAssessReport;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 评测报告Mapper类
 */
class AiAssessReportMapper extends AbstractMapper
{
    /**
     * @var AiAssessReport
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = AiAssessReport::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        
        // 难度,1-3
        if (isset($params['difficulty']) && $params['difficulty'] !== '') {
            $query->where('difficulty', '=', $params['difficulty']);
        }

        // 是否完成评测:1完成,0未完成
        if (isset($params['is_assess_done']) && $params['is_assess_done'] !== '') {
            $query->where('is_assess_done', '=', $params['is_assess_done']);
        }

        // 知识点总数
        if (isset($params['knows_count']) && $params['knows_count'] !== '') {
            $query->where('knows_count', '=', $params['knows_count']);
        }

        // 已掌握知识点数量
        if (isset($params['knows_mastered_count']) && $params['knows_mastered_count'] !== '') {
            $query->where('knows_mastered_count', '=', $params['knows_mastered_count']);
        }

        // 未掌握知识点数量
        if (isset($params['knows_unmastered_count']) && $params['knows_unmastered_count'] !== '') {
            $query->where('knows_unmastered_count', '=', $params['knows_unmastered_count']);
        }

        // 知识点掌握率
        if (isset($params['knows_mastered_rate']) && $params['knows_mastered_rate'] !== '') {
            $query->where('knows_mastered_rate', '=', $params['knows_mastered_rate']);
        }

        // 题目总数
        if (isset($params['ques_count']) && $params['ques_count'] !== '') {
            $query->where('ques_count', '=', $params['ques_count']);
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