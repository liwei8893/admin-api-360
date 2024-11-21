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

use App\Ai\Mapper\AiQuesDetailStatMapper;
use App\Ai\Model\AiQuesDetailStat;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractService;

/**
 * 题目详情统计服务类
 */
class AiQuesDetailStatService extends AbstractService
{
    /**
     * @var AiQuesDetailStatMapper
     */
    public $mapper;

    public function __construct(AiQuesDetailStatMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    // 新增题目统计
    public function addQuesStat(int $quesId, int $isRight): Model|Builder|AiQuesDetailStat
    {
        $mod = AiQuesDetailStat::query()->where('ques_id', $quesId)->first();
        if (!$mod) {
            // 没有就初始化数据新增
            return AiQuesDetailStat::query()->create([
                'ques_id' => $quesId,
                'total_user_count' => 1,
                'ques_correct_count' => $isRight ? 1 : 0,
                'ques_incorrect_count' => $isRight ? 0 : 1,
                'ques_correct_rate' => $isRight ? 100 : 0,
            ]);
        }
        // 有就更新数据
        ++$mod->total_user_count;
        if ($isRight) {
            ++$mod->ques_correct_count;
        } else {
            ++$mod->ques_incorrect_count;
        }
        // 计算总数
        $quesTotalCount = $mod->total_user_count + $mod->ques_correct_count;
        // 计算正确率
        $mod->ques_correct_rate = round(($mod->ques_correct_count / $quesTotalCount) * 100, 2);
        $mod->save();
        return $mod;
    }
}
