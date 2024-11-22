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

namespace App\Ai\Service;

use App\Ai\Mapper\AiAssessReportMapper;
use App\Ai\Model\AiAssessQuesDetail;
use App\Ai\Model\AiAssessReport;
use App\Ai\Model\AiKnowsClassify;
use App\Ai\Model\AiQuestion;
use Hyperf\Collection\Collection;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;

/**
 * 评测报告服务类
 */
class AiAssessReportService extends AbstractService
{
    /**
     * @var AiAssessReportMapper
     */
    public $mapper;

    #[Inject]
    protected AiKnowsClassifyService $AiKnowsClassifyService;

    public function __construct(AiAssessReportMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getOne(int $id): array
    {
        $mod = AiAssessReport::query()->find($id);
        if (!$mod) {
            throw new NormalStatusException('评测报告不存在');
        }
        $mod->load(['quesDetail' => function ($query) {
            $query->with(['question']);
        }]);
        return $mod->toArray();
    }

    #[Transaction]
    public function gen($params): array
    {
        $difficulty = $params['difficulty'];
        $knowsId = $params['knows_id'];
        $grade = $params['grade'];
        $subject = $params['subject'];
        $userId = user('app')->getId();
        // 查找所有的子知识点
        $classifyList = $this->AiKnowsClassifyService->findAllChildren($knowsId);
        // 查找父级知识点
        $classifyParentList = AiKnowsClassify::query()->where('status', 1)->whereIn('id', $knowsId)->get();
        // 查找题目
        $questionList = $this->randomQuestionList($classifyList->pluck('id')->toArray(), $difficulty);
        // 保存到评测报告表
        $insetReport = [
            'user_id' => $userId,
            'knows_id' => $knowsId,
            'knows_name' => $classifyParentList->pluck('name')->toArray(),
            'grade' => $grade,
            'subject' => $subject,
            'difficulty' => $difficulty,
            'knows_count' => $questionList->pluck('classify_id')->unique()->count(),
            'ques_count' => $questionList->count(),
        ];
        $reportMod = AiAssessReport::query()->create($insetReport);

        // 保存题目到详情表
        $insetQuesDetail = [];
        /* @var AiQuestion $ques */
        foreach ($questionList as $ques) {
            $knowsClassify = $ques->knowsClassify;
            $knows_level1 = $classifyParentList->where('id', $knowsClassify->parent_id)->first();
            if ($knows_level1) {
                $knows_level1_name = $knows_level1->name;
            } else {
                $knows_level1_name = '';
            }
            $insetQuesDetail[] = [
                'user_id' => $userId,
                'assess_report_id' => $reportMod->id,
                'ques_id' => $ques->id,
                'knows_level1_id' => $knowsClassify->parent_id,
                'knows_level1_name' => $knows_level1_name,
                'knows_level2_id' => $knowsClassify->id,
                'knows_level2_name' => $knowsClassify->name,
                'knows_difficulty' => $knowsClassify->difficulty,
                'rec_answer_duration' => $this->genRecAnswerDuration($knowsClassify->difficulty),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        AiAssessQuesDetail::query()->insert($insetQuesDetail);
        return $reportMod->toArray();
    }

    /**
     * @param array $classifyId
     * @param int $difficulty
     * @return Collection
     */
    public function randomQuestionList(array $classifyId, int $difficulty): Collection
    {
        $questionList = AiQuestion::query()
            ->with(['knowsClassify'])
            ->where('status', 1)
            ->where('ques_difficulty', $difficulty)
            ->whereIn('classify_id', $classifyId)
            ->get();
        if ($questionList->count() >= 10) {
            $questionList = $questionList->random(10);
        } else if ($questionList->count() >= 5) {
            $questionList = $questionList->random(5);
        } else {
            $questionList = $questionList->random($questionList->count());
        }
        return $questionList;
    }

    /**
     * 生成建议的答题时间
     * @param int $difficulty
     * @return int
     */
    public function genRecAnswerDuration(int $difficulty): int
    {
        // 难度1 答题时间1分钟
        // 难度2 答题时间2分钟
        // 难度3 答题时间3分钟
        // 后期根据题目平均答题时间修改
        return $difficulty * 60;
    }

    /**
     * 完成报告
     * @param array $params
     * @return bool
     */
    public function finish(array $params): bool
    {
        $id = $params['id'];
        $reportMod = AiAssessReport::query()->find($id);
        if (!$reportMod) {
            throw new NormalStatusException('评测报告不存在');
        }
        $quesDetail = $reportMod->quesDetail;
        // 计算未掌握知识点数量 knows_unmastered_count
        $reportMod->knows_unmastered_count = $quesDetail->where('is_right', 0)->pluck('knows_level2_id')->unique()->count();
        // 计算已掌握知识点数量 knows_mastered_count
        $reportMod->knows_mastered_count = $reportMod->knows_count - $reportMod->knows_unmastered_count;
        // 计算知识点掌握率 knows_mastered_rate
        $reportMod->knows_mastered_rate = round(($reportMod->knows_mastered_count / $reportMod->knows_count) * 100, 2);
        // 计算正确题目数 ques_correct_count
        $reportMod->ques_correct_count = $quesDetail->where('is_right', 1)->count();
        // 计算错误题目数 ques_incorrect_count
        $reportMod->ques_incorrect_count = $quesDetail->where('is_right', 0)->count();
        // 计算题目正确率 ques_correct_rate
        $reportMod->ques_correct_rate = round(($reportMod->ques_correct_count / $reportMod->ques_count) * 100, 2);
        // 报告设置为完成
        $reportMod->is_assess_done = 1;
        return $reportMod->save();
    }
}
