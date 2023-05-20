<?php

declare(strict_types=1);

namespace App\Pay\Service;

use App\Order\Model\Order;
use App\Order\Model\OrderPayment;
use App\Order\Service\OrderSignupService;
use App\Pay\Mapper\PayAppMapper;
use App\Pay\Model\PayAuth;
use App\Pay\Model\PayLink;
use App\Score\Event\ScoreAddEvent;
use App\Users\Model\User;
use App\Users\Service\UserSalePlatformService;
use App\Users\Service\UsersAppLoginService;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\OfficialAccount\Application;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yansongda\Pay\Exception\ContainerException;
use Yansongda\Pay\Exception\InvalidParamsException;
use Yansongda\Pay\Pay;
use Yansongda\Supports\Collection;

/**
 * 公众号配置服务类.
 */
class PayAppService extends AbstractService
{
    /**
     * @var PayAppMapper
     */
    #[Inject]
    public $mapper;

    #[Inject]
    protected PayAuthService $authService;

    #[Inject]
    protected PayLinkService $payLinkService;

    #[Inject]
    protected UsersAppLoginService $appLoginService;

    #[Inject]
    protected UserSalePlatformService $userSalePlatformService;

    #[Inject]
    protected OrderSignupService $orderSignupService;

    /**
     * @param array $params
     * @return string
     * @throws InvalidArgumentException
     */
    public function wxAuth(array $params): string
    {
        $authId = $params['authId'];
        /* @var PayAuth $authConfig */
        $authConfig = $this->authService->read($authId);
        if (! $authConfig) {
            throw new NormalStatusException('认证配置不存在!');
        }
        $config = ['app_id' => $authConfig->appid, 'secret' => $authConfig->app_secret];
        $app = new Application($config);
        return $app->getOAuth()->scopes(['snsapi_base'])->redirect($params['redirectUrl']);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function OAuths(array $params): array
    {
        $authId = $params['authId'];
        $code = $params['code'];
        /* @var PayAuth $authConfig */
        $authConfig = $this->authService->read($authId);
        if (! $authConfig) {
            throw new NormalStatusException('认证配置不存在!');
        }
        $config = ['app_id' => $authConfig->appid, 'secret' => $authConfig->app_secret];
        $app = new Application($config);
        $user = $app->getOauth()->userFromCode($code);
        return ['openId' => $user->getId()];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function payLinkVip(array $params): Collection|array
    {
        $payLinkId = $params['payLinkId'];
        /** @var PayLink $payLinkModel */
        $payLinkModel = $this->payLinkService->read($payLinkId);
        if (! $payLinkModel) {
            throw new NormalStatusException('商品配置不存在!');
        }

        /** @var User $userModel */
        $userModel = $this->appLoginService->mapper->checkUserByMobile($params['mobile'], User::COMMON_FIELDS);
        if (! $userModel) {
            $registerData = ['mobile' => $params['mobile'], 'platform' => $payLinkModel->platform, 'remark' => $payLinkModel->remark];
            if (! empty($params['userName'])) {
                $registerData['user_name'] = $params['userName'];
            }
            $userModel = $this->appLoginService->register($registerData);
        } else {
            // 注册了就修改学员属性
            $upData = ['remark' => $payLinkModel->remark];
            // 检测是否有平台，没有平台更改平台
            if (empty($userModel->platform)) {
                $upData['platform'] = $payLinkModel->platform;
                $upData = $this->userSalePlatformService->withPlatformNum($upData);
            }
            $userModel->update($upData);
        }
        $courseModel = $payLinkModel->course;
        if (! $courseModel) {
            throw new NormalStatusException('当前链接未关联课程!');
        }
        /* @var Order $orderModel */
        $orderModel = $userModel->orders()->where('deleted_at', 0)->where('shop_id', $courseModel->id)->first();
        // 处理年级课程限制
        // 已购买课程直接返回
        if ($orderModel && $orderModel->pay_states === Order::PAY_SUCCESS) {
            throw new NormalStatusException('当前账号已购买课程,如需续费请联系课程顾问!');
        }
        $insetInfo = [
            'indate' => $payLinkModel->indate,
            'platform' => $userModel->platform,
            'remark' => $payLinkModel->remark,
            'actual_price' => $payLinkModel->price,
            'order_price' => $payLinkModel->price * 100,
            'user_id' => $userModel->id,
            'coupon_id' => $payLinkModel->id,
            'pay_type' => 1,
            'pay_states' => 1,
        ];
        // 有订单,没完成,更新订单,返回订单号继续支付
        if ($orderModel && $orderModel->pay_states !== Order::PAY_SUCCESS) {
            $orderModel->update($insetInfo);
        } elseif (! $orderModel) {
            // 没有订单,创建订单
            $insertData = $this->orderSignupService->handleInsertCourseData($insetInfo, $courseModel);
            $insertData['pay_states'] = 1;
            $orderModel = Order::create($insertData);
        }
        if ($params['openId']) {
            return $this->wxPay(['order_number' => $orderModel->order_number, 'payLinkId' => $payLinkId, 'openid' => $params['openId']]);
        }
        return $this->wapPay(['order_number' => $orderModel->order_number, 'payLinkId' => $payLinkId]);
    }

    /**
     * 微信支付.
     * @throws Exception
     */
    public function wxPay(array $params): Collection
    {
        if (empty($params['order_number'])) {
            throw new NormalStatusException('订单号不能为空');
        }
        if (empty($params['payLinkId'])) {
            throw new NormalStatusException('商品配置不存在');
        }
        if (empty($params['openid'])) {
            throw new NormalStatusException('openid不能为空');
        }
        // 6 微信支付
        $params['tag_type'] = 6;
        // 获取微信支付配置
        $payConfig = $this->mapper->getPayConfig((int) $params['payLinkId']);
        // 生成支付单
        $paymentParams = $this->mapper->handleCreatePayment($params);
        // 微信公众号jsapi支付
        return $this->mapper->jsApiPay($payConfig, $paymentParams);
    }

    /**
     * 网页支付.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function wapPay(array $params): array
    {
        if (empty($params['order_number'])) {
            throw new NormalStatusException('订单号不能为空');
        }
        if (empty($params['payLinkId'])) {
            throw new NormalStatusException('商品配置不存在');
        }
        // 4 网页支付
        $params['tag_type'] = 4;
        // 获取微信支付配置
        $payConfig = $this->mapper->getPayConfig((int) $params['payLinkId']);
        // 生成支付单
        $paymentParams = $this->mapper->handleCreatePayment($params);
        // 微信公众号jsapi支付
        $result = $this->mapper->wapPay($payConfig, $paymentParams);
        return ['h5_url' => $result->h5_url];
    }

    /**
     * 微信支付回调验签.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function wxNotify(int $id): ResponseInterface
    {
        $payConfig = $this->mapper->getPayConfig($id);
        try {
            $pay = Pay::wechat($payConfig)->callback(ServerRequestInterface::class);
            $paymentModel = OrderPayment::query()->where('payment_number', $pay['out_trade_no'])->first();
            if ($paymentModel) {
                $paymentModel->trade_no = $pay['transaction_id'];
                $paymentModel->pay_price = $pay['total_fee'] * 0.01;
                $paymentModel->status = 1;
                $paymentModel->payed_time = time();
                $paymentModel->save();
                $this->handleWXNotify($paymentModel->order_number);
            }
        } catch (ContainerException|InvalidParamsException $e) {
        }
        return Pay::wechat()->success();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handleWXNotify(string $order_number): void
    {
        // 获取付款订单号
        $orderModel = $this->mapper->getOrderModel($order_number);
        if (! $orderModel) {
            throw new NormalStatusException('订单查询失败!');
        }
        // 只付款订单
        if (str_contains($order_number, '_onlyPay')) {
            // 续费单改成成功状态
            $orderModel->pay_states = 7;
            $orderModel->save();
            return;
        }
        Order::query()->whereIn('order_number', [$order_number, $order_number . '_0', $order_number . '_1'])
            ->update(['pay_states' => 7, 'updated_at' => time(), 'created_at' => time()]);
        // 新增会员时增加积分
        if ($orderModel->pay_states === Order::PAY_SUCCESS && $orderModel->shop_id === User::VIP_TYPE_SUPER) {
            event(new ScoreAddEvent('init', (int) $orderModel->user_id, $orderModel->id));
        }
    }
}
