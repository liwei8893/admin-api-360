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

use App\Ai\Model\AiAssessQuesDetail;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 评测题目明细Mapper类
 */
class AiAssessQuesDetailMapper extends AbstractMapper
{
    /**
     * @var AiAssessQuesDetail
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = AiAssessQuesDetail::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        
        // 评测报告表ID
        if (isset($params['assess_report_id']) && $params['assess_report_id'] !== '') {
            $query->where('assess_report_id', '=', $params['assess_report_id']);
        }

        // 题目ID
        if (isset($params['ques_id']) && $params['ques_id'] !== '') {
            $query->where('ques_id', '=', $params['ques_id']);
        }

        // 1级知识点ID
        if (isset($params['knows_level1_id']) && $params['knows_level1_id'] !== '') {
            $query->where('knows_level1_id', '=', $params['knows_level1_id']);
        }

        // 1级知识点名称
        if (isset($params['knows_level1_name']) && $params['knows_level1_name'] !== '') {
            $query->where('knows_level1_name', 'like', '%'.$params['knows_level1_name'].'%');
        }

        // 2级知识点ID
        if (isset($params['knows_level2_id']) && $params['knows_level2_id'] !== '') {
            $query->where('knows_level2_id', '=', $params['knows_level2_id']);
        }

        // 2级知识点名称
        if (isset($params['knows_level2_name']) && $params['knows_level2_name'] !== '') {
            $query->where('knows_level2_name', 'like', '%'.$params['knows_level2_name'].'%');
        }

        // 知识点难度
        if (isset($params['knows_difficulty']) && $params['knows_difficulty'] !== '') {
            $query->where('knows_difficulty', '=', $params['knows_difficulty']);
        }

        // 建议答题时间
        if (isset($params['rec_answer_duration']) && $params['rec_answer_duration'] !== '') {
            $query->where('rec_answer_duration', '=', $params['rec_answer_duration']);
        }

        // 是否正确
        if (isset($params['is_right']) && $params['is_right'] !== '') {
            $query->where('is_right', '=', $params['is_right']);
        }

        // 答题时间
        if (isset($params['user_answer_duration']) && $params['user_answer_duration'] !== '') {
            $query->where('user_answer_duration', '=', $params['user_answer_duration']);
        }

        return $query;
    }
}