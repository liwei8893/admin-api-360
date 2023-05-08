<?php

declare(strict_types=1);

namespace App\Pay\Mapper;

use App\Order\Model\Order;
use App\Order\Model\OrderPayment;
use App\Pay\Model\PayLink;
use Exception;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractMapper;
use Mine\Exception\NormalStatusException;
use Mine\MineRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Yansongda\Pay\Pay;
use Yansongda\Supports\Collection;

/**
 * 公众号配置Mapper类.
 */
class PayAppMapper extends AbstractMapper
{
    /**
     * @var PayLink
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = PayLink::class;
    }

    public function getPayConfig(int $payLinkId): array
    {
        /** @var PayLink $payLinkModel */
        $payLinkModel = $this->read($payLinkId);
        if (! $payLinkModel) {
            throw new NormalStatusException('商品配置不存在!');
        }
        // 获取微信支付配置
        $payConfig = $payLinkModel->payConfig->toArray();
        $payConfig['mch_public_cert_path'] = BASE_PATH . '/storage/cert' . $payConfig['mch_public_cert_path'];
        $payConfig['notify_url'] = \Hyperf\Support\env('APP_URL') . 'pay/app/wxNotify/' . $payLinkId;
        return [
            'wechat' => ['default' => $payConfig],
            'logger' => [
                'enable' => true,
                'file' => BASE_PATH . '/runtime/logs/pay/pay.log',
                'level' => \Hyperf\Support\env('APP_ENV') === 'dev' ? 'debug' : 'info', // 建议生产环境等级调整为 info，开发环境为 debug
                'type' => 'daily', // optional, 可选 daily.
                'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
            ],
        ];
    }

    /**
     * @throws Exception
     */
    public function handleCreatePayment(array $params): array
    {
        // 获取订单
        $orderModel = $this->getOrderModel($params['order_number']);
        if (! $orderModel) {
            throw new NormalStatusException('订单查询失败!');
        }
        // 判断订单状态
        $this->handleOrderStatus($orderModel);
        // 生成payment订单号
        $paymentNumber = $this->getPaymentSn();
        $params['payment_number'] = $paymentNumber;
        $params['title'] = $this->handlePayTitle($orderModel->shop_name);
        $params['order_price'] = $orderModel->order_price;
        // 生成支付单
        $paymentStatus = $this->createPayment($orderModel, $params);
        if (! $paymentStatus) {
            throw new NormalStatusException('生成支付单失败!');
        }
        return $params;
    }

    public function getOrderModel(string $order_number): Order|Model|Builder|null
    {
        // 获取订单
        return Order::query()->where('order_number', $order_number)->first();
    }

    public function handleOrderStatus(Order $orderModel): void
    {
        // 判断订单是否已支付
        if ($orderModel->pay_states !== 1) {
            throw new NormalStatusException('订单已完成!');
        }
        // 判断订单是否删除
        if ($orderModel->deleted_at !== 0) {
            throw new NormalStatusException('订单已删除!');
        }
        // 判断订单没退费
        if ($orderModel->status === 2) {
            throw new NormalStatusException('订单已退费!');
        }
    }

    /**
     * @throws Exception
     */
    public function getPaymentSn(): string
    {
        $rand_num = random_int(0, 99999);
        do {
            if ($rand_num === 99999) {
                $rand_num = 0;
            }
            ++$rand_num;
            $payment_number = 'WX' . date('ymdHis') . str_pad((string) $rand_num, 5, '0', STR_PAD_LEFT);
            $row = OrderPayment::query()->where(['payment_number' => $payment_number])->count();
        } while ($row);
        return $payment_number;
    }

    public function handlePayTitle(string $title): string
    {
        $titleLength = strlen($title);
        if ($titleLength > 8) {
            return mb_substr($title, 0, 8, 'UTF-8');
        }
        return $title;
    }

    public function createPayment(Order $orderModel, array $params): bool
    {
        $payType = 'wx';
        if (isset($params['pay_type']) && (int) $params['pay_type'] === 2) {
            $payType = 'alipay';
        }
        $paymentModel = new OrderPayment();
        $paymentModel->payment_number = $params['payment_number'];
        $paymentModel->order_number = $orderModel['order_number'];
        $paymentModel->pay_price = $orderModel['order_price'] * 0.01;
        $paymentModel->pay_app_id = $payType;
        $paymentModel->tag_type = $params['tag_type'] ?? 6;
        $paymentModel->subject = $this->handlePayTitle($orderModel['shop_name']);
        $paymentModel->user_id = $orderModel['user_id'];
        $paymentModel->begin_time = time();
        $paymentModel->status = 0;
        return $paymentModel->save();
    }

    public function jsApiPay(array $config, array $params): Collection
    {
        $order = [
            'out_trade_no' => $params['payment_number'],
            'description' => $params['title'],
            'amount' => ['total' => $params['order_price']],
            'payer' => ['openid' => $params['openid']],
        ];
        return Pay::wechat($config)->mp($order);
    }

    /**
     * @param mixed $config
     * @param mixed $params
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function wapPay(array $config, array $params): Collection
    {
        $request = container()->get(MineRequest::class);
        $order = [
            'out_trade_no' => $params['payment_number'],
            'description' => $params['title'],
            'amount' => [
                'total' => $params['order_price'],
            ],
            'scene_info' => [
                'payer_client_ip' => $request->ip(),
                'h5_info' => [
                    'type' => 'Wap',
                ],
            ],
        ];
        return Pay::wechat($config)->wap($order);
    }

    public function aliWapPay(array $config,array $params): ResponseInterface
    {
        $order = [
            'out_trade_no' => $params['payment_number'],
            'total_amount' => $params['order_price'] * 0.01,
            'subject' => $params['title'],
        ];
        return Pay::alipay($config)->wap($order);
    }
}
