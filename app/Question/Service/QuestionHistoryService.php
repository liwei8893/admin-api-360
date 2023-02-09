<?php

declare(strict_types=1);

namespace App\Question\Service;

use App\Question\Mapper\QuestionHistoryMapper;
use App\Question\Model\Question;
use App\Question\Model\QuestionHistory;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Database\Model\Collection;
use Hyperf\Di\Annotation\Inject;
use JsonException;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;

/**
 * 错题表服务类.
 */
class QuestionHistoryService extends AbstractService
{
    /**
     * @var QuestionHistoryMapper
     */
    public $mapper;

    #[Inject]
    protected QuestionService $questionService;

    public function __construct(QuestionHistoryMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 获取做题排行榜,缓存一个小时.
     */
    #[Cacheable(prefix: 'ranking', value: 'question', ttl: 3600)]
    public function getRanking(): Collection|array
    {
        return $this->mapper->getRanking()->map(function ($item) {
            if (! empty($item['users'])) {
                if ($item['users']['mobile'] === $item['users']['user_name']) {
                    $item['users']['user_name'] = substr_replace($item['users']['user_name'], '****', 3, 4);
                }
                unset($item['users']['mobile']);
            }
            return $item;
        });
    }

    /**
     * 获取用户排行榜名次,缓存1小时.
     */
    #[Cacheable(prefix: 'ranking', value: 'questionMe_#{userId}', ttl: 3600)]
    public function getRankingMe(int $userId): array
    {
        return ['ranking' => $this->mapper->getRankingMe($userId)];
    }

    /**
     * 获取每月做题数报告,缓存24小时.
     */
    #[Cacheable(prefix: 'report', value: 'question_#{userId}', ttl: 86400)]
    public function getReport(int $userId): array
    {
        $monthMap = collect([
            'month01' => ['month' => '01', 'num' => 0],
            'month02' => ['month' => '02', 'num' => 0],
            'month03' => ['month' => '03', 'num' => 0],
            'month04' => ['month' => '04', 'num' => 0],
            'month05' => ['month' => '05', 'num' => 0],
            'month06' => ['month' => '06', 'num' => 0],
            'month07' => ['month' => '07', 'num' => 0],
            'month08' => ['month' => '08', 'num' => 0],
            'month09' => ['month' => '09', 'num' => 0],
            'month10' => ['month' => '10', 'num' => 0],
            'month11' => ['month' => '11', 'num' => 0],
            'month12' => ['month' => '12', 'num' => 0],
        ]);
        $data = $this->mapper->getReportByMonth()
            ->keyBy(fn ($item) => 'month' . $item['month']);
        $total = $this->mapper->getReportByTotal($userId);
        $rate = $this->mapper->getRankingRate($userId);
        return [
            'chart' => $monthMap->merge($data)->values()->toArray(),
            'total' => $total,
            'rate' => $rate,
        ];
    }

    /**
     * 过滤空格
     */
    public function deleteHtmlTrim(string $str): string
    {
        // 清除字符串两边的空格
        $str = trim($str);
        // 替换开头空字符
        $str = preg_replace("/^[\\s\v" . chr(227) . chr(128) . ']+/', '', $str);
        // 替换结尾空字符
        $str = preg_replace("/[\\s\v" . chr(227) . chr(128) . ']+$/', '', $str);
        return trim($str); // 返回字符串
    }

    /**
     * @throws JsonException
     */
    public function submit(array $params): array
    {
        $userId = user('app')->getId();
        $quesId = $params['ques_id'];
        /* @var QuestionHistory $model */
        $model = $this->mapper->first(['user_id' => $userId, 'ques_id' => $quesId]);
        // 题目不能重复提交
        if ($model) {
            throw new NormalStatusException('题目已做过!');
        }
        /* @var Question $questionModel 获取题目模型 */
        $questionModel = $this->questionService->read($quesId);
        if (! $questionModel) {
            throw new NormalStatusException('未查询到题目!');
        }
        // 获取正确答案
        $rightAnswer = $questionModel->right_answer;
        // 用户答案
        $userAnswer = $params['user_answer'];
        // 保存的数据
        $saveData = ['user_id' => $userId, 'ques_id' => $quesId, 'user_answer' => $userAnswer, 'is_right' => 0];
        // 比对答案
        // "title": "单选题", "key": "1"
        // "title": "多选题", "key": "2"
        // "title": "判断题", "key": "4"
        if (in_array($questionModel->ques_type, [1, 2, 4, 5])) {
            $handleRightAnswer = strtoupper(trim(strip_tags($rightAnswer)));
            $handleUserAnswer = strtoupper(trim(strip_tags($userAnswer)));
            // "title": "问答题", "key": "5"
            if ($questionModel->ques_type === 5) {
                if (mb_strripos($handleRightAnswer, $handleUserAnswer) !== false || mb_strripos($handleUserAnswer, $handleRightAnswer) !== false) {
                    $saveData['is_right'] = 1;
                }
            } elseif ($handleUserAnswer === $handleRightAnswer) {
                $saveData['is_right'] = 1;
            }
            $saveData['right_answer'] = $handleRightAnswer;
        }

        // "title": "填空题", "key": "6"
        if ($questionModel->ques_type === 6) {
            // 用户答案
            $handleUserAnswer = strtoupper(trim(strip_tags($userAnswer)));
            // 去掉用户答案空格
            $handleUserAnswerArr = [];
            foreach (explode('$$$', $handleUserAnswer) as $item) {
                $handleUserAnswerArr[] = $this->deleteHtmlTrim($item);
            }
            $handleUserAnswer = implode('$$$', $handleUserAnswerArr);
            // 正确答案
            $handleRightAnswer = json_decode($questionModel->ques_option, true, 512, JSON_THROW_ON_ERROR);
            // 去掉正确答案空格
            $handleRightAnswerArr = [];
            foreach ($handleRightAnswer as $item) {
                // 去掉变态空格
                $handleRightAnswerArr[] = $this->deleteHtmlTrim($item['content']);
            }
            $handleRightAnswer = implode('$$$', $handleRightAnswerArr);
            if (mb_strripos($handleUserAnswer, $handleRightAnswer) !== false) {
                $saveData['is_right'] = 1;
            }
            $saveData['right_answer'] = $handleRightAnswer;
        }
        // 保存到数据库
        $this->save($saveData);
        // TODO 添加积分事件
        return $saveData;
    }

    public function changeErrorCollect(array $params): bool
    {
        $userId = user('app')->getId();
        $model = $this->mapper->first(['user_id' => $userId, 'id' => $params['id']]);
        if (! $model) {
            throw new NormalStatusException('只能收藏自己的题目!');
        }
        $is_collect = $model['is_collect'] === 1 ? 0 : 1;
        return $this->update($params['id'], ['is_collect' => $is_collect]);
    }
}
