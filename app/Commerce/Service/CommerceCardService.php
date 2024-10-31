<?php

declare(strict_types=1);

namespace App\Commerce\Service;

use App\Commerce\Mapper\CommerceCardMapper;
use App\Commerce\Model\CommerceCard;
use App\Commerce\Model\CommerceCardUsage;
use App\Course\Model\CourseBasis;
use App\Order\Model\Order;
use App\Order\Service\OrderSignupService;
use App\Score\Event\ScoreAddEvent;
use App\System\Service\SmsService;
use App\Users\Model\User;
use App\Users\Service\UsersAppLoginService;
use Exception;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 电商管理服务类.
 */
class CommerceCardService extends AbstractService
{
    /**
     * @var CommerceCardMapper
     */
    public $mapper;

    #[Inject]
    protected SmsService $smsService;

    #[Inject]
    protected UsersAppLoginService $loginService;

    #[Inject]
    protected OrderSignupService $orderSignupService;

    public function __construct(CommerceCardMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 激活电商卡片.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function activateCard(array $params): bool
    {
        $cardId = $params['card_id'];
        $mobile = $params['mobile'];
        $smsCode = $params['sms_code'];
        // 短信验证码验证
        $this->smsService->checkSmsCaptcha((string)$mobile, (string)$smsCode);
        // 判断是否使用
        /** @var CommerceCard $cardModel */
        $cardModel = $this->mapper->findCardByCardId((int)$cardId);
        if (!$cardModel) {
            throw new NormalStatusException('卡号不正确,请核对后输入!');
        }
        if ($cardModel->status === 1) {
            throw new NormalStatusException('卡号已被使用,如有疑问请联系官网客服!');
        }
        // 开启事务,cardModel状态变更为已使用,没有注册的注册,注册了直接报名.
        // 开始注册
        /** @var User $userModel */
        $userModel = $this->loginService->mapper->checkUserByMobile($params['mobile'], User::COMMON_FIELDS);
        if (!$userModel) {
            $registerData = ['mobile' => $mobile, 'platform' => 'H', 'remark' => '电商卡片激活'];
            $userModel = $this->loginService->register($registerData);
        }
        // 报名课程
        $courseModel = $cardModel->course;
        if (!$courseModel) {
            throw new NormalStatusException('当前卡片未关联课程,请联系官网客服!');
        }
        /* @var Order $orderModel */
        $orderModel = $userModel->orders()->where('deleted_at', 0)->where('shop_id', $courseModel->id)->first();
        // 已购买课程直接返回
        if ($orderModel && $orderModel->pay_states === Order::PAY_SUCCESS) {
            throw new NormalStatusException('当前账号已购买课程,如需续费请联系官网客服!');
        }
        // 卡片都是一年有效期,默认365
        $insetInfo = [
            'indate' => 365,
            'platform' => $userModel->platform,
            'remark' => '会员卡片激活',
            'money' => 365,
            'actual_price' => 365,
            'order_price' => 365 * 100,
            'real_year' => 1,
            'user_id' => $userModel->id,
            'coupon_id' => $cardModel->id,
            'pay_type' => 9, // 电商卡
            'pay_states' => 7, // 直接设置为成功状态
        ];
        // 有订单,没完成,更新订单,返回订单号继续支付
        try {
            DB::beginTransaction();
            // 卡片设置为已使用
            $cardModel->status = 1;
            $cardModel->save();
            // 添加使用记录
            $cardUsageModel = new CommerceCardUsage(['user_id' => $userModel->id]);
            $cardModel->usage()->save($cardUsageModel);
            if ($orderModel && $orderModel->pay_states !== Order::PAY_SUCCESS) {
                $orderModel->update($insetInfo);
            } elseif (!$orderModel) {
                // 没有订单,创建订单
                $insertData = $this->orderSignupService->handleInsertCourseData($insetInfo, $courseModel);
                $orderModel = Order::create($insertData);
            }
            // 如果是950,年级限制为1-9
            if ($courseModel->id === User::VIP_TYPE_SUPER) {
                $orderModel->orderGrade()->sync([1, 2, 5, 7, 9, 11, 12, 13, 14]);
            }
            // 如果是高中单科,没有特色课的,送特色课
            if (in_array($courseModel->id, User::VIP_TYPE_HIGH)) {
                /* @var Order $featureOrderModel */
                $featureOrderModel = $userModel->orders()->normalOrder()->where('shop_id', 1436)->first();
                if (!$featureOrderModel) {
                    $featureInsetInfo = [...$insetInfo, 'remark' => '电商卡片激活赠送特色课', 'money' => 0, 'real_year' => 0];
                    $featureCourseModel = CourseBasis::query()->find(1436);
                    $insertData = $this->orderSignupService->handleInsertCourseData($featureInsetInfo, $featureCourseModel);
                    Order::create($insertData);
                }
            }
            // 增加积分
            event(new ScoreAddEvent('init', $userModel->id, $orderModel->id));
            DB::commit();
        } catch (Exception $e) {
            // 报错就回滚
            DB::rollBack();
            throw new NormalStatusException('系统错误,请联系官网客服激活卡片!');
        }
        return true;
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        // 生成card_id
        $data['card_id'] = $this->generateCardId();
        return parent::save($data);
    }

    public function generateCard($params): array
    {
        // 生成的卡号
        $cardArr = [];
        for ($i = 0; $i < $params['num']; ++$i) {
            $cardArr[] = $this->generateCardId();
        }
        if (count($cardArr) !== count(array_unique($cardArr))) {
            throw new NormalStatusException('生成的卡号有重复数据,请重新生成!');
        }
        // 存入数据库
        $insertData = [];
        // 需要导出的数据
        $exportData = [];
        foreach ($cardArr as $cardId) {
            $exportData[] = ['card_id' => $cardId];
            $insertData[] = [
                'card_id' => $cardId,
                'course_id' => $params['course_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        try {
            $status = $this->mapper->batchInsertData($insertData);
            if (!$status) {
                throw new NormalStatusException('生成的卡号有重复数据,请重新生成!');
            }
        } catch (Exception $e) {
            throw new NormalStatusException('生成的卡号有重复数据,请重新生成!');
        }
        return $exportData;
    }

    public function generateCardId($len = 8): string
    {
        $code = '';
        $characters = '123456789';

        for ($i = 0; $i < $len; ++$i) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $code;
    }

    /**
     * 需要处理导出数据时,重写函数.
     */
    protected function handleExportData(array &$data): void
    {
        $data['status'] = $data['status'] === 0 ? '未使用' : '使用';
    }
}
