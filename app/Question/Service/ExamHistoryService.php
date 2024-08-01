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

namespace App\Question\Service;

use App\Question\Mapper\ExamHistoryMapper;
use App\Question\Model\Exam;
use App\Question\Model\ExamHistory;
use Hyperf\Cache\Annotation\Cacheable;
use JsonException;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;

/**
 * 试卷记录服务类
 */
class ExamHistoryService extends AbstractService
{
    /**
     * @var ExamHistoryMapper
     */
    public $mapper;

    public function __construct(ExamHistoryMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 错题收藏或取消
     * @param array $params
     * @return bool
     */
    public function changeErrorCollect(array $params): bool
    {
        $userId = user('app')->getId();
        $model = $this->mapper->first(['user_id' => $userId, 'id' => $params['id']]);
        if (!$model) {
            throw new NormalStatusException('只能收藏自己的题目!');
        }
        $is_collect = $model['is_collect'] === 1 ? 0 : 1;
        return $this->update($params['id'], ['is_collect' => $is_collect]);
    }

    /**
     * 保存做题记录
     * @param array $params
     * @return array
     * @throws JsonException
     */
    public function appSave(array $params): array
    {
        $userId = user('app')->getId();

        /* @var ExamHistory $model */
        $model = $this->mapper->first(['user_id' => $userId, 'exam_id' => $params['exam_id']]);
        // 题目不能重复提交
        if ($model) {
            throw new NormalStatusException('题目已做过!');
        }
        $examModel = Exam::query()->find($params['exam_id']);
        if (!$examModel) {
            throw new NormalStatusException('未查询到题目!');
        }
        // 获取正确答案
        $rightAnswer = $examModel->right_answer;
        // 用户答案
        $userAnswer = $params['user_answer'];
        $handleUserAnswer = strtoupper(trim(strip_tags($userAnswer)));
        // 验证用户答案长度
        if (mb_strlen($userAnswer, 'UTF-8') >= 500) {
            throw new NormalStatusException('答案长度过长！');
        }
        // 保存的数据
        $saveModel = new ExamHistory();
        $saveModel->user_id = $userId;
        $saveModel->exam_id = $examModel->id;
        $saveModel->user_answer = $userAnswer;
        $saveModel->is_right = 0;
        // 比对答案
        // "title": "单选题", "key": "1"
        // "title": "多选题", "key": "2"
        // "title": "判断题", "key": "4"
        if (in_array($examModel->ques_type, [1, 2, 4, 5])) {
            $handleRightAnswer = strtoupper(trim(strip_tags($rightAnswer)));

            // "title": "问答题", "key": "5"
            if ($examModel->ques_type === 5) {
                if (mb_strripos($handleRightAnswer, $handleUserAnswer) !== false || mb_strripos($handleUserAnswer, $handleRightAnswer) !== false) {
                    $saveModel->is_right = 1;
                }
            } elseif ($handleUserAnswer === $handleRightAnswer) {
                $saveModel->is_right = 1;
            }
        }

        // "title": "填空题", "key": "6"
        if ($examModel->ques_type === 6) {
            // 去掉用户答案空格
            $handleUserAnswerArr = [];
            foreach (explode('$$$', $handleUserAnswer) as $item) {
                $handleUserAnswerArr[] = $this->deleteHtmlTrim($item);
            }
            $handleUserAnswer = implode('$$$', $handleUserAnswerArr);
            // 正确答案
            $handleRightAnswer = json_decode($examModel->ques_option, true, 512, JSON_THROW_ON_ERROR);
            // 去掉正确答案空格
            $handleRightAnswerArr = [];
            foreach ($handleRightAnswer as $item) {
                // 去掉变态空格
                $handleRightAnswerArr[] = $this->deleteHtmlTrim($item['content']);
            }
            $handleRightAnswer = implode('$$$', $handleRightAnswerArr);
            if (mb_strripos($handleUserAnswer, $handleRightAnswer) !== false) {
                $saveModel->is_right = 1;
            }
        }
        // 保存到数据库
        $saveModel->save();
        return $saveModel->refresh()->toArray();
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
     * 获取做题排行榜,缓存一个小时.
     */
    #[Cacheable(prefix: 'ranking', value: 'exam', ttl: 86400)]
    public function getRanking(): array
    {
        return $this->mapper->getRanking()->map(function ($item) {
            if (!empty($item['users'])) {
                if ($item['users']['mobile'] === $item['users']['user_name']) {
                    $item['users']['user_name'] = substr_replace($item['users']['user_name'], '****', 3, 4);
                }
                unset($item['users']['mobile']);
            }
            return $item;
        })->toArray();
    }

    /**
     * 获取用户排行榜名次,缓存1小时.
     */
    #[Cacheable(prefix: 'ranking', value: 'examMe_#{userId}', ttl: 86400)]
    public function getRankingMe(int $userId): array
    {
        return ['ranking' => $this->mapper->getRankingMe($userId)];
    }

    public function getRankingCustomDate(array $params): array
    {
        return $this->mapper->getRanking($params)->map(function ($item) {
            if (!empty($item['users'])) {
                if ($item['users']['mobile'] === $item['users']['user_name']) {
                    $item['users']['user_name'] = substr_replace($item['users']['user_name'], '****', 3, 4);
                }
                unset($item['users']['mobile']);
            }
            return $item;
        })->toArray();
    }

    public function getRankingMeCustomDate(int $userId, array $params): array
    {
        return ['ranking' => $this->mapper->getRankingMe($userId, $params)];
    }
}
