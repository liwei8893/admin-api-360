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
            'difficulty' => $difficulty,
            'knows_count' => $questionList->pluck('classify_id')->unique()->count(),
            'ques_count' => $questionList->count(),
        ];
        $reportMod = AiAssessReport::query()->create($insetReport);

        // 保存题目到详情表
        $insetQuesDetail = [];
        /* @var AiQuestion $ques */
        foreach ($questionList as $ques) {
            $knows_level1 = $classifyParentList->where('id', $ques->knowsClassify->parent_id)->first();
            if ($knows_level1) {
                $knows_level1_name = $knows_level1->name;
            } else {
                $knows_level1_name = '';
            }
            $insetQuesDetail[] = [
                'user_id' => $userId,
                'assess_report_id' => $reportMod->id,
                'ques_id' => $ques->id,
                'knows_level1_id' => $ques->knowsClassify->parent_id,
                'knows_level1_name' => $knows_level1_name,
                'knows_level2_id' => $ques->knowsClassify->id,
                'knows_level2_name' => $ques->knowsClassify->name,
                'knows_difficulty' => $ques->knowsClassify->difficulty,
                'rec_answer_duration' => $this->genRecAnswerDuration($difficulty),
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
}
