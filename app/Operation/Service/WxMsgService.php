<?php

declare(strict_types=1);

namespace App\Operation\Service;

use App\Operation\Mapper\WxMsgMapper;
use App\Operation\Model\WxMsg;
use App\Operation\Queue\Producer\SendWxMsgProducer;
use App\Users\Model\User;
use Carbon\Carbon;
use Hyperf\Amqp\Producer;
use Hyperf\Di\Annotation\Inject;
use JsonException;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Pengxuxu\HyperfWechat\EasyWechat;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
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
                    'first' => ['value' => $dataMsg->first],
                    'keyword1' => ['value' => $dataMsg->keyword1],
                    'keyword2' => ['value' => $dataMsg->keyword2],
                    'keyword3' => ['value' => $dataMsg->keyword3],
                    'remark' => ['value' => $dataMsg->remark],
                ],
            ];

            $message = new SendWxMsgProducer($data);
            $this->producer->produce($message);
        }
        $dataMsg->status = WxMsg::SENT;
        return $dataMsg->save();
    }

    /**
     * 发送微信消息.
     */
    public function sendWxMsg(array $data): bool
    {
        //        $config = config('wechat.official_account.default');
        //        logger('QueueLog')->info('微信消息配置:' . json_encode($config));
        //        $app = new Application($config);
        try {
            $app = EasyWechat::officialAccount();
            $api = $app->getClient();
            $response = $api->postJson('/cgi-bin/message/template/send', $data);
            logger('QueueLog')->info('微信消息StatusCode:' . $response->getStatusCode());
            logger('QueueLog')->info('微信消息response:' . $response->getContent());
            $json = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
            return $response->getStatusCode() === 200 && $json['errcode'] === 0;
        } catch (JsonException|NotFoundExceptionInterface|ContainerExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            logger('QueueLog')->error('微信消息消费错误：' . json_encode($e->getMessage()));
            return false;
        }
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
        if (! empty($data['send_time'])) {
            $data['send_time'] = Carbon::parse($data['send_time'])->format('Y-m-d H:i');
        }
    }
}
