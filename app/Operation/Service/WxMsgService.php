<?php

declare(strict_types=1);

namespace App\Operation\Service;

use App\Operation\Mapper\WxMsgMapper;
use App\Operation\Model\WxMsg;
use App\Operation\Queue\Producer\SendWxMsgProducer;
use App\Users\Model\User;
use Carbon\Carbon;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\OfficialAccount\Application;
use Hyperf\Amqp\Producer;
use Hyperf\Di\Annotation\Inject;
use JsonException;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * 微信消息服务类.
 */
class WxMsgService extends AbstractService
{
    /**
     * @var WxMsgMapper
     */
    public $mapper;

    #[Inject]
    protected Producer $producer;

    public function __construct(WxMsgMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        $this->handleData($data);
        return parent::save($data);
    }

    /**
     * 更新一条数据.
     */
    public function update(int $id, array $data): bool
    {
        $this->handleData($data);
        return parent::update($id, $data);
    }

    /**
     * @throws JsonException
     */
    public function addQueueWxMsg(): bool
    {
        // 查询未发送的一条消息
        $dataMsg = $this->mapper->getFirstUnsentMsg();
        if (! $dataMsg) {
            throw new NormalStatusException('暂无消息发送！');
        }
        $userData = $this->mapper->getSendUsers();
        if ($userData->isEmpty()) {
            throw new NormalStatusException('未查询到发送人员！');
        }
        // 循环推送到队列
        /* @var User $user */
        foreach ($userData as $user) {
            $data = [
                'url' => 'https://h5.hxt360.com',
                'touser' => $user->wxgzh_openid,
                'template_id' => $dataMsg->tmp_id,
                'client_msg_id' => $dataMsg->id,
                'data' => [
                    'first' => [
                        'value' => $dataMsg->first, // 自定义参数
                        'color' => '#ff5101', // 自定义颜色
                    ],
                    'keyword1' => [
                        'value' => $dataMsg->keyword1, // 自定义参数
                        'color' => '#ff5101', // 自定义颜色
                    ],
                    'keyword2' => [
                        'value' => $dataMsg->keyword2, // 自定义参数
                        'color' => '#ff5101', // 自定义颜色
                    ],
                    'keyword3' => [
                        'value' => $dataMsg->keyword3, // 自定义参数
                        'color' => '#ff5101', // 自定义颜色
                    ],
                    'remark' => [
                        'value' => $dataMsg->remark, // 自定义参数
                        'color' => '#ff5101', // 自定义颜色
                    ],
                ],
            ];

            $message = new SendWxMsgProducer($data);
            $this->producer->produce($message);
        }
        $dataMsg->status = WxMsg::SENT;
        return $dataMsg->save();
    }

    /**
     * @throws JsonException
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function sendWxMsg(array $data): bool
    {
        $config = config('wechat.official_account.default');
        $app = new Application($config);
        $api = $app->getClient();
        $response = $api->postJson('/cgi-bin/message/template/send', $data);
        $json = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        console()->info($response->getContent());
        return $response->getStatusCode() === 200 && $json['errcode'] === 0;
    }

    protected function handleData(array &$data): void
    {
        // 默认模版 ID
        if (empty($data['tmp_id'])) {
            $data['tmp_id'] = 'kkK3xAv-Zk3PhcRa6JwlDsITGOF0zLmHs80mM6awdc0';
        }
        if (! empty($data['keyword2'])) {
            $data['keyword2'] = Carbon::parse($data['keyword2'])->format('Y-m-d H:i');
        }
    }
}
