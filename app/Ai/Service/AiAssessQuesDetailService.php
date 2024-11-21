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

use App\Ai\Mapper\AiAssessQuesDetailMapper;
use App\Ai\Model\AiAssessQuesDetail;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;

/**
 * 评测题目明细服务类
 */
class AiAssessQuesDetailService extends AbstractService
{
    /**
     * @var AiAssessQuesDetailMapper
     */
    public $mapper;

    #[Inject]
    protected AiQuesDetailStatService $aiQuesDetailStatService;

    public function __construct(AiAssessQuesDetailMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 用户提交题目答案
     * @param array $params
     * @return bool
     */
    public function submit(array $params): bool
    {
        $userId = user()->getId();
        $mod = AiAssessQuesDetail::Query()->where('id', $params['id'])->where('user_id', $userId)->first();
        if (!$mod) {
            throw new NormalStatusException('评测题目不存在');
        }
        $mod->user_answer = $params['user_answer'];
        $mod->user_answer_duration = $params['user_answer_duration'];
        // 判断是否答对 //
        $questionModel = $mod->question;
        // 获取正确答案
        $rightAnswer = $questionModel->right_answer;
        // 用户答案
        $userAnswer = $params['user_answer'];
        // 比对答案
        // "title": "单选题", "key": "1"
        // "title": "多选题", "key": "2"
        // "title": "判断题", "key": "4"
        if (in_array($questionModel->ques_type, [1, 2, 4])) {
            $handleRightAnswer = strtoupper(trim(strip_tags($rightAnswer)));
            $handleUserAnswer = strtoupper(trim(strip_tags($userAnswer)));
            if ($handleUserAnswer === $handleRightAnswer) {
                $mod->is_right = 1;
            }
        }
        // 标记为已答题
        $mod->is_answer = 1;
        $mod->save();
        // 插入题目统计
        $this->aiQuesDetailStatService->addQuesStat($mod->ques_id, $mod->is_right);
        return true;
    }
}
