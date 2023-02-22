<?php

declare(strict_types=1);

namespace App\Score\Service;

use App\Course\Model\CourseBasis;
use App\Order\Service\OrderSignupService;
use App\Score\Mapper\ScoreShopMapper;
use App\Score\Model\ScoreShop;
use App\Users\Model\User;
use App\Users\Service\UserScoreService;
use App\Users\Service\UsersService;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;

/**
 * 积分管理服务类.
 */
class ScoreShopService extends AbstractService
{
    /**
     * @var ScoreShopMapper
     */
    #[Inject]
    public $mapper;

    #[Inject]
    protected UsersService $usersService;

    #[Inject]
    protected UserScoreService $userScoreService;

    #[Inject]
    protected OrderSignupService $orderSignupService;

    public function getAvatarPageList($params): array
    {
        $params['orderBy'] = ['score', 'sort'];
        $params['orderType'] = ['asc', 'desc'];
        $params['withShop'] = true;
        $params['shop_type'] = 'avatar';
        return $this->getPageList($params);
    }

    public function getCoursePageList($params): array
    {
        $params['orderBy'] = ['score', 'sort'];
        $params['orderType'] = ['asc', 'desc'];
        $params['withShop'] = true;
        $params['shop_type'] = 'courseBasis';
        return $this->getPageList($params);
    }

    /**
     * 获取用户积分详情分页.
     */
    public function getUserScorePage(array $params): array
    {
        $params['user_id'] = user('app')->getId();
        return $this->userScoreService->getUserScorePage($params);
    }

    /**
     * 积分兑换课程头像.
     * @throws Exception
     */
    #[Transaction]
    public function exchange(array $params): array
    {
        // 查询商品信息
        /* @var ScoreShop $shopInfo */
        $shopInfo = $this->read($params['id']);
        if (! $shopInfo) {
            throw new NormalStatusException('兑换商品不存在!');
        }
        // 用户模型
        /* @var User $userModel */
        $userModel = $this->usersService->read(user('app')->getId());
        if (! $userModel) {
            throw new NormalStatusException('用户不存在!');
        }
        // 兑换商品所需要的积分
        $score = $shopInfo['score'];
        // 已拥有积分
        $userScore = $userModel['score'];
        if ($score > $userScore) {
            throw new NormalStatusException('兑换商品所需要的积分不足!');
        }
        // 兑换头像类
        if ($shopInfo['shop_type'] === 'avatar') {
            // 查询头像
            $avatarModel = $shopInfo['shop'];
            // 判断是否已经拥有
            $hasAvatar = $userModel->avatarTable()->where('id', $avatarModel->id)->exists();
            if ($hasAvatar) {
                throw new NormalStatusException('该商品已拥有,请兑换其他商品!');
            }
            // 扣除积分
            $scoreState = $this->userScoreService->changeScore([
                'user_id' => $userModel->id,
                'origin_id' => $avatarModel->id,
                'channel' => $avatarModel->type === 1 ? '兑换头像' : '兑换头像框',
                'channel_type' => $avatarModel->type === 1 ? 7 : 8,
                'score' => $score,
                'type' => 0,
            ]);
            if (! $scoreState) {
                throw new NormalStatusException('兑换失败请重试!');
            }
            // 保存头像
            $userModel->avatarTable()->attach($avatarModel->id);
            // 使用头像
            if ($avatarModel->type === 1) {
                $userModel->avatar = $avatarModel->url;
            } else {
                $userModel->avatar_frame = $avatarModel->url;
            }
            $userModel->save();
            $userModel->fresh();
            return $userModel->toArray();
        }
        // 兑换课程类
        if ($shopInfo['shop_type'] === 'courseBasis') {
            /* @var CourseBasis $courseModel 查询课程 */
            $courseModel = $shopInfo->shop;
            // 判断是否已经拥有
            $orderModel = $userModel->orders()
                ->where('deleted_at', 0)
                ->where('shop_id', $courseModel->id)->first();
            $insetInfo = [
                'indate' => 365,
                'platform' => $userModel->platform,
                'remark' => '积分兑换课程',
                'money' => 0,
                'user_id' => $userModel->id,
                'pay_type' => 3,
                'pay_states' => 7,
                'audit_status' => 0,
            ];
            $scoreRecordInfo = [
                'user_id' => $userModel->id,
                'origin_id' => $courseModel->id,
                'channel' => '兑换课程',
                'channel_type' => 9,
                'score' => $score,
                'type' => 0,
            ];
            // 没有订单,创建订单
            if (! $orderModel) {
                $this->orderSignupService->handleInsertCourseData($insetInfo, $courseModel);
                // 扣除积分
                $scoreState = $this->userScoreService->changeScore($scoreRecordInfo);
                if (! $scoreState) {
                    throw new NormalStatusException('兑换失败请重试!');
                }
                $userModel->fresh();
                return $userModel->toArray();
            }
            // 有订单,没完成,更新订单
            if (isset($orderModel['pay_states']) && $orderModel['pay_states'] !== 7) {
                $this->orderSignupService->update($orderModel['id'], array_merge($insetInfo, [
                    'order_price' => $insetInfo['money'] * 100,
                    'actual_price' => $insetInfo['money'],
                ]));
                // 扣除积分
                $scoreState = $this->userScoreService->changeScore($scoreRecordInfo);
                if (! $scoreState) {
                    throw new NormalStatusException('兑换失败请重试!');
                }
                $userModel->fresh();
                return $userModel->toArray();
            }
            // 有订单,完成
            throw new NormalStatusException('该商品已拥有,请兑换其他商品!');
        }
        return [];
    }
}
