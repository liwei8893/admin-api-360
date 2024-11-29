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

    public function getKDA(int $id): array
    {
        $mod = AiAssessReport::query()->find($id);
        if (!$mod) {
            throw new NormalStatusException('评测报告不存在');
        }
        $model = AiAssessReport::query();
//        $model = $model->whereJsonContains('knows_id', $mod->knows_id);
        // kda列表
        $kdaMod = $model->selectRaw('kda, count(*) as count')->groupBy('kda')->orderBy('kda')->get();
        // 总人数
        $userTotal = $kdaMod->sum('count');
        // kda最高值
        $kdaMax = $kdaMod->max('kda');
        // kda最小值
        $kdaMin = $kdaMod->min('kda');
        // kda平均值
        $kdaAvg = $kdaMod->avg('kda');
        // 众数-数量最多的值
        $kdaMostValue = $kdaMod->sortByDesc('count')->first()->kda;
        // 排名
        $kdaRank = $kdaMod->where('kda', '<', $mod->kda)->sum('count');
        $kdaRank = ($kdaRank / ($userTotal - 1)) * 100;
        return [
            'userTotal' => $userTotal,
            'kdaMax' => (float)$kdaMax,
            'kdaMin' => (float)$kdaMin,
            'kdaAvg' => round((float)$kdaAvg, 2),
            'kdaMostValue' => (float)$kdaMostValue,
            'kdaRank' => (int)$kdaRank,
            'kdaList' => $kdaMod->toArray(),
        ];
    }

    public function getAppPageList(array $params): array
    {
        $params['orderBy'] = ['id'];
        $params['orderType'] = ['desc'];
        $params['user_id'] = user('app')->getId();
        return $this->mapper->getPageList($params);
    }

    public function getOne(int $id): array
    {
        $mod = AiAssessReport::query()->find($id);
        if (!$mod) {
            throw new NormalStatusException('评测报告不存在');
        }
        $mod->load(['user:id,user_name', 'quesDetail' => function ($query) {
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
        if ($questionList->isEmpty()) {
            throw new NormalStatusException('暂无题目');
        }
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
            $quesStat = $ques->quesStat;
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
                'rec_answer_duration' => $this->genRecAnswerDuration($knowsClassify->difficulty, $quesStat->avg_answer_duration ?? 0),
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
            ->with(['knowsClassify', 'quesStat'])
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
     * @param int $difficulty 难度
     * @param int $avgAnswerDuration 平均答题时间
     * @return int
     */
    public function genRecAnswerDuration(int $difficulty, int $avgAnswerDuration): int
    {

        // 难度1 答题时间1分钟
        // 难度2 答题时间2分钟
        // 难度3 答题时间3分钟
        $base = $difficulty * 60;
        if ($avgAnswerDuration === 0) {
            $avgAnswerDuration = $difficulty * 60;
        }
        // 调整系数
        $t = 0.5;
        return (int)($base + $t * ($avgAnswerDuration - $base));
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

        // 计算kda指标,1计算平均建议答题时间,2计算平均答题时间,3计算平均难度
        // K = （建议答题时间 - 实际答题时间） / （建议答题时间 - 最短时间）
        $avgRecAnswerDuration = $quesDetail->avg('rec_answer_duration'); // 平均建议答题时间
        $avgAnswerDuration = $quesDetail->avg('user_answer_duration'); // 平均答题时间
        $k = (($avgRecAnswerDuration - $avgAnswerDuration) / ($avgRecAnswerDuration - 1)) * 100;
        // D = （实际难度 - 最小难度） / （最大难度 - 最小难度）
        $avgDifficulty = $quesDetail->avg('knows_difficulty'); // 平均难度
        $d = (($avgDifficulty - 1) / (3 - 1)) * 100;
        // A（正确率）
        $a = $reportMod->ques_correct_rate;
        $kda = max(0, ($k + $d + $a) / 3);
        $reportMod->kda = round($kda, 1);

        // 报告设置为完成
        $reportMod->is_assess_done = 1;
        return $reportMod->save();
    }
}
