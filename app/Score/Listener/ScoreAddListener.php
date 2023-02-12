<?php

declare(strict_types=1);

namespace App\Score\Listener;

use App\Order\Model\Order;
use App\Order\Model\UsersRenew;
use App\Question\Mapper\QuestionHistoryMapper;
use App\Score\Event\ScoreAddEvent;
use App\Users\Mapper\UserCourseRecordMapper;
use App\Users\Mapper\UserScoreMapper;
use App\Users\Model\User;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

#[Listener]
class ScoreAddListener implements ListenerInterface
{
    #[Inject]
    protected UserScoreMapper $mapper;

    public function listen(): array
    {
        return [ScoreAddEvent::class];
    }

    /**
     * Handle the Event when the event is triggered, all listeners will
     * complete before the event is returned to the EventDispatcher.
     */
    public function process(object $event): void
    {
        $type = $event->type;
        $userId = $event->userId;
        $originId = $event->originId;
        if (method_exists($this, $type)) {
            $this->{$type}($userId, $originId);
        }
    }

    /**
     * 分享积分：上传1个视频或图片文字介绍学习经验，经审核通过得2个积分。
     * 1签到,2会员积分,3认证积分,4听课积分,5做题积分,6分享积分.
     */
    public function share(int $userId, int $originId): void
    {
        $channel_type = 6;
        $userModel = User::query()->find($userId);
        // 检测当天是否获取过积分
        $scoreCount = $this->mapper->getCurScoreCount($userModel->id, $channel_type);
        if ($scoreCount >= 1) {
            return;
        }
        // 没获取过,积分加2
        $this->changeScore([
            'user_id' => $userModel->id,
            'origin_id' => $originId,
            'channel' => '分享积分',
            'channel_type' => $channel_type,
            'score' => 2,
            'type' => 1,
        ]);
    }

    /**
     * 做题积分：做题3道可获得1个积分，每个账户每天最多3个积分；
     * 1签到,2会员积分,3认证积分,4听课积分,5做题积分,6分享积分.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function question(int $userId, int $originId): void
    {
        $channel_type = 5;
        $userModel = User::query()->find($userId);
        // 检测当天做题数,3 6 9获取积分
        $questionCount = container()->get(QuestionHistoryMapper::class)->getCurQuestionCount($userModel->id);
        if (!in_array($questionCount, [3, 6, 9])) {
            return;
        }
        // 检测当天是否获取过积分
        $scoreCount = $this->mapper->getCurScoreCount($userModel->id, $channel_type);
        if ($scoreCount >= 3) {
            return;
        }
        // 没获取过,积分加1
        $this->changeScore([
            'user_id' => $userModel->id,
            'origin_id' => $originId,
            'channel' => '做题积分',
            'channel_type' => $channel_type,
            'score' => 1,
            'type' => 1,
        ]);
    }

    /**
     * 听课积分：听课30分钟可获得1个积分，每个账户每天最多3个积分；
     * 1签到,2会员积分,3认证积分,4听课积分,5做题积分,6分享积分.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function course(int $userId): void
    {
        $channel_type = 4;
        $userModel = User::query()->find($userId);
        // 检测当天是否获取过积分
        $scoreCount = $this->mapper->getCurScoreCount($userModel->id, $channel_type);
        if ($scoreCount >= 3) {
            return;
        }

        $courseRecordTodayTask = container()->get(UserCourseRecordMapper::class);
        // 查询用户当天听课时长
        $todayModel = $courseRecordTodayTask->getTodayDataByUserId($userModel->id);
        if (!$todayModel) {
            return;
        }
        // 计算达成30分钟次数
        $timeCount = floor($todayModel['record_time'] / (30 * 60));
        // 如果相等表示当前区间的积分已经领取
        if ((int)$timeCount === $scoreCount) {
            return;
        }

        $originId = $todayModel['id'];
        // 没获取过,积分加1
        $this->changeScore([
            'user_id' => $userModel->id,
            'origin_id' => $originId,
            'channel' => '听课积分',
            'channel_type' => $channel_type,
            'score' => 1,
            'type' => 1,
        ]);
    }

    /**
     * 认证积分：填写完整的“姓名”、“电话”、“年级”认证获得10个积分；
     * 1签到,2会员积分,3认证积分,4听课积分,5做题积分,6分享积分.
     * @param mixed $originId
     */
    public function auth(int $userId, int $originId): void
    {
        // TODO 前端弹出完善初始信息时处理
        $channel_type = 3;
        $userModel = User::query()->find($userId);
        // 检测用户是否获取过,一个用户只能获取一次
        $has = $this->mapper->first(['channel_type' => $channel_type, 'user_id' => $userModel->id]);
        if ($has) {
            return;
        }
        // 没获取过,积分加10
        $this->changeScore([
            'user_id' => $userModel->id,
            'origin_id' => $originId,
            'channel' => '认证积分',
            'channel_type' => $channel_type,
            'score' => 10,
            'type' => 1,
        ]);
    }

    /**
     * 会员积分：成为黄冈优课优题正式会员，获得等值消费的原始积分；
     * 1签到,2会员积分,3认证积分,4听课积分,5做题积分,6分享积分.
     * @param int $originId 订单ID
     */
    public function init(int $userId, int $originId): void
    {
        $channel_type = 2;
        $userModel = User::query()->find($userId);
        // 查询订单
        $orderModel = Order::query()->find($originId);
        // 查询不到订单,或者实际付款金额为0,或者订单状态不为完成 结束
        if (!$orderModel || (int)$orderModel->shop_id !== 950 || !$orderModel->actual_price || (int)$orderModel->pay_states !== 7) {
            return;
        }
        $this->changeScore([
            'user_id' => $userModel->id,
            'origin_id' => $originId,
            'channel' => '会员积分',
            'channel_type' => $channel_type,
            'score' => $orderModel->actual_price,
            'type' => 1,
        ]);
    }

    /**
     * 会员积分：成为黄冈优课优题正式会员，获得等值消费的原始积分；
     * 1签到,2会员积分,3认证积分,4听课积分,5做题积分,6分享积分.
     * @param int $originId 续费表ID
     */
    public function renew(int $userId, int $originId): void
    {
        $channel_type = 2;
        $userModel = User::query()->find($userId);
        // 查询订单
        $renewModel = UsersRenew::query()->find($originId);
        // 查询不到续费单,续费金额为0,续费状态不为成功
        if (!$renewModel || !$renewModel->money || (int)$renewModel->status !== 1 || (int)$renewModel->audit_status !== 0) {
            return;
        }
        $this->changeScore([
            'user_id' => $userModel->id,
            'origin_id' => $originId,
            'channel' => '续费积分',
            'channel_type' => $channel_type,
            'score' => $renewModel->money,
            'type' => 1,
        ]);
    }

    /**
     * 退费扣除会员积分；
     * 1签到,2会员积分,3认证积分,4听课积分,5做题积分,6分享积分.
     * @param int $originId 订单ID
     */
    public function courseRefund(int $userId, int $originId): void
    {
        $channel_type = 2;
        $userModel = User::query()->find($userId);
        // 查询订单
        $orderModel = Order::query()->find($originId);
        // 查询不到订单,或者订单状态不为完成 结束
        if (!$orderModel || (int)$orderModel->status !== Order::STATUS_REFUND) {
            return;
        }
        // 扣除的积分=订单金额+续费金额
        $money = $orderModel->actual_price;
        $renewModel = $orderModel->usersRenew()->where('status', 1)->where('audit_status', 0)->get();
        if ($renewModel->isNotEmpty()) {
            $money += $renewModel->sum('money');
        }
        $this->changeScore([
            'user_id' => $userModel->id,
            'origin_id' => $originId,
            'channel' => '退费积分',
            'channel_type' => $channel_type,
            'score' => $money,
            'type' => 0,
        ]);
    }

    /**
     * 变更积分.
     */
    #[Transaction]
    public function changeScore(array $data): bool
    {
        $userId = $data['user_id'];
        $originId = $data['origin_id'];
        $channel = $data['channel'];
        $channelType = $data['channel_type'];
        $score = $data['score'];
        $type = $data['type'];
        // 插入积分详情表
        $data = [
            'user_id' => $userId,
            'origin_id' => $originId,
            'channel' => $channel,
            'channel_type' => $channelType,
            'score' => $score,
        ];
        if ((int)$type === 1) {
            // 保存增加积分详情
            $this->mapper->saveInUserScore($data);
            // 增加用户积分
            $states = $this->mapper->incrementUserScore($userId, (int)$score);
        } else {
            // 保存减少积分详情
            $this->mapper->saveUnUserScore($data);
            // 减少用户积分
            $states = $this->mapper->decrementUserScore($userId, (int)$score);
        }

        if (!$states) {
            throw new NormalStatusException('系统错误');
        }
        return true;
    }
}
